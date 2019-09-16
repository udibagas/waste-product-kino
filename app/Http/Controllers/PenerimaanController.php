<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PenerimaanRequest;
use App\Penerimaan;
use App\Events\PenerimaanSubmitted;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return Penerimaan::selectRaw('penerimaans.*, users.name AS [user]')
            ->join('users', 'users.id', '=', 'penerimaans.user_id')
            ->when($request->keyword, function ($q) use ($request) {
                return $q->where('no_sj_keluar', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('lokasi_asal', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('lokasi_terima', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('penerima', 'LIKE', '%' . $request->keyword . '%');
            })->when($request->lokasi_asal_id, function ($q) use ($request) {
                return $q->whereIn('lokasi_asal_id', $request->lokasi_asal_id);
            })->when($request->lokasi_terima_id, function ($q) use ($request) {
                return $q->whereIn('lokasi_terima_id', $request->lokasi_terima_id);
            })->when($request->status, function ($q) use ($request) {
                return $q->whereIn('status', $request->status);
            })->when($request->user()->role == \App\User::ROLE_USER, function ($q) use ($request) {
                return $q->where('lokasi_terima_id', $request->user()->location_id);
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function show(Penerimaan $penerimaan)
    {
        return $penerimaan->with(['items']);
    }

    public function store(PenerimaanRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // header
                $id = DB::table('penerimaans')->insertGetId([
                    'tanggal' => $request->tanggal,
                    'no_sj_keluar' => $request->no_sj_keluar,
                    'penerima' => $request->penerima,
                    'keterangan' => $request->keterangan,
                    'lokasi_asal' => $request->lokasi_asal,
                    'lokasi_terima' => $request->lokasi_terima,
                    'lokasi_asal_id' => $request->lokasi_asal_id,
                    'lokasi_terima_id' => $request->lokasi_terima_id,
                    'status' => $request->status,
                    'user_id' => $request->user()->id
                ]);

                // item
                DB::table('penerimaan_items')->insert(array_map(function($item) use ($id) {
                    return [
                        'penerimaan_id' => $id,
                        'kategori_barang_id' => $item['kategori_barang_id'],
                        'eun' => $item['eun'],
                        'timbangan_manual_kirim' => $item['timbangan_manual_kirim'],
                        'timbangan_manual_terima' => $item['timbangan_manual_terima'],
                        'keterangan' => isset($item['keterangan']) ? $item['keterangan'] : ''
                    ];
                }, $request->items));

                $penerimaan = Penerimaan::find($id);

                if ($request->status == Penerimaan::STATUS_SUBMITTED) {
                    event(new PenerimaanSubmitted($penerimaan));
                }

                return $penerimaan;
            });
        } catch (\Exception $e) {
            return response(['message' => 'Data gagal disimpan. '.$e->getMessage()], 500);
        }
    }

    public function update(PenerimaanRequest $request, Penerimaan $penerimaan)
    {
        try {
            DB::transaction(function () use ($request, $penerimaan) {
                // header
                DB::table('penerimaans')->where('id', $penerimaan->id)->update([
                    'tanggal' => $request->tanggal,
                    'penerima' => $request->penerima,
                    'keterangan' => $request->keterangan,
                    'status' => $request->status,
                ]);

                // delete all item
                DB::table('penerimaan_items')->where('penerimaan_id', $penerimaan->id)->delete();

                // add new item
                DB::table('penerimaan_items')->insert(array_map(function($item) use ($penerimaan) {
                    return [
                        'penerimaan_id' => $penerimaan->id,
                        'kategori_barang_id' => $item['kategori_barang_id'],
                        'eun' => $item['eun'],
                        'timbangan_manual_kirim' => $item['timbangan_manual_kirim'],
                        'timbangan_manual_terima' => $item['timbangan_manual_terima'],
                        'keterangan' => isset($item['keterangan']) ? $item['keterangan'] : ''
                    ];
                }, $request->items));

                if ($request->status == Penerimaan::STATUS_SUBMITTED) {
                    event(new PenerimaanSubmitted($penerimaan));
                }

                return $penerimaan;
            });
        } catch (\Exception $e) {
            return response(['message' => 'Data gagal disimpan. '.$e->getMessage()], 500);
        }
    }

    public function destroy(Penerimaan $penerimaan)
    {
        if ($penerimaan->status > 0) {
            return response(['message' => 'Penerimaan sudah di submit.'], 500);
        }

        $penerimaan->delete();
        return ['message' => 'ok'];
    }
}
