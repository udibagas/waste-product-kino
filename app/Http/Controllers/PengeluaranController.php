<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PengeluaranRequest;
use App\Pengeluaran;
use App\Events\PengeluaranSubmitted;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return Pengeluaran::selectRaw('pengeluarans.*, users.name AS [user]')
            ->join('users', 'users.id', '=', 'pengeluarans.user_id')
            ->when($request->keyword, function ($q) use ($request) {
                return $q->where('no_sj', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('lokasi_asal', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('lokasi_terima', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('penerima', 'LIKE', '%' . $request->keyword . '%');
            })->when($request->lokasi_asal_id, function($q) use ($request) {
                return $q->whereIn('lokasi_asal_id', $request->lokasi_asal_id);
            })->when($request->lokasi_terima_id, function ($q) use ($request) {
                return $q->whereIn('lokasi_terima_id', $request->lokasi_terima_id);
            })->when($request->status, function ($q) use ($request) {
                return $q->whereIn('status', $request->status);
            })->when($request->user()->role == \App\User::ROLE_USER, function ($q) use ($request) {
                return $q->where('lokasi_asal_id', $request->user()->location_id);
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function store(PengeluaranRequest $request)
    {
        $no_sj = 0;
        $lastRecord = Pengeluaran::whereRaw('YEAR(tanggal) = ? ', [date('Y')])->orderBy('id', 'DESC')->first();

        if ($lastRecord) {
            $no_sj = (int) substr($lastRecord->no_sj, 0, 4);
        }

        try {
            DB::transaction(function () use ($request, $no_sj) {
                // header
                $id = DB::table('pengeluarans')->insertGetId([
                    'tanggal' => $request->tanggal,
                    'no_sj' => str_pad($no_sj + 1, 4, "0", STR_PAD_LEFT).date('/m/Y').'/OUT/BB',
                    'jembatan_timbang' => $request->jembatan_timbang,
                    'lokasi_asal_id' => $request->lokasi_asal_id,
                    'lokasi_terima_id' => $request->lokasi_terima_id,
                    'lokasi_asal' => $request->lokasi_asal,
                    'lokasi_terima' => $request->lokasi_terima,
                    'status' => $request->status,
                    'user_id' => $request->user()->id
                ]);

                // item
                DB::table('pengeluaran_items')->insert(array_map(function($item) use ($id) {
                    return [
                        'pengeluaran_id' => $id,
                        'kategori_barang_id' => $item['kategori_barang_id'],
                        'eun' => $item['eun'],
                        'timbangan_manual' => floatval($item['timbangan_manual'])
                    ];
                }, $request->items));

                $pengeluaran = Pengeluaran::find($id);

                if ($request->status == Pengeluaran::STATUS_SUBMITTED) {
                    event(new PengeluaranSubmitted($pengeluaran));
                }

                return $pengeluaran;
            });
        } catch (\Exception $e) {
            return response(['message' => 'Data gagal disimpan. '.$e->getMessage()], 500);
        }
    }

    public function update(PengeluaranRequest $request, Pengeluaran $pengeluaran)
    {
        try {
            DB::transaction(function () use ($request, $pengeluaran) {
                // header
                DB::table('pengeluarans')->where('id', $pengeluaran->id)->update([
                    'tanggal' => $request->tanggal,
                    'jembatan_timbang' => $request->jembatan_timbang,
                    'lokasi_terima_id' => $request->lokasi_terima_id,
                    'lokasi_terima' => $request->lokasi_terima,
                    'status' => $request->status,
                ]);

                // drop item first
                DB::table('pengeluaran_items')->where('pengeluaran_id', $pengeluaran->id)->delete();

                // add new item
                DB::table('pengeluaran_items')->insert(array_map(function($item) use ($pengeluaran) {
                    return [
                        'pengeluaran_id' => $pengeluaran->id,
                        'kategori_barang_id' => $item['kategori_barang_id'],
                        'eun' => $item['eun'],
                        'timbangan_manual' => floatval($item['timbangan_manual'])
                    ];
                }, $request->items));

                if ($request->status == Pengeluaran::STATUS_SUBMITTED) {
                    event(new PengeluaranSubmitted($pengeluaran));
                }

                return $pengeluaran;
            });
        } catch (\Exception $e) {
            return response(['message' => 'Data gagal disimpan. '.$e->getMessage()], 500);
        }
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        if ($pengeluaran->status > 0) {
            return response(['message' => 'Pengeluaran sudah di submit.'], 500);
        }

        $pengeluaran->delete();
        return ['message' => 'ok'];
    }

    /**
     * List pengeluaran yang sudah disbmit untuk dropdown di penerimaan
     *
     */
    public function getList(Request $request)
    {
        return Pengeluaran::where('status', Pengeluaran::STATUS_SUBMITTED)
            ->when($request->user()->role == \App\User::ROLE_USER, function($q) use ($request) {
                return $q->where('lokasi_terima_id', $request->user()->location_id);
            })->get();
    }

    public function getLastRecord(Request $request)
    {
        return Pengeluaran::whereRaw('YEAR(tanggal) = ? ', [$request->tahun])
            ->orderBy('id', 'DESC')->first();
    }
}
