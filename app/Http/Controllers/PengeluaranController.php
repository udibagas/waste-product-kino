<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PengeluaranRequest;
use App\Pengeluaran;
use App\PengeluaranItem;
use App\Events\PengeluaranSubmitted;

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
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $pengeluaran = Pengeluaran::create($input);
        $pengeluaran->items()->createMany($request->items);

        if ($request->status == Pengeluaran::STATUS_SUBMITTED) {
            event(new PengeluaranSubmitted($pengeluaran));
        }

        return $pengeluaran;
    }

    public function update(PengeluaranRequest $request, Pengeluaran $pengeluaran)
    {
        $pengeluaran->update($request->all());

        foreach ($request->items as $i)
        {
            if (isset($i['id'])) {
                PengeluaranItem::find($i['id'])->update($i);
            } else {
                $pengeluaran->items()->create($i);
            }
        }

        if ($request->status == Pengeluaran::STATUS_SUBMITTED) {
            event(new PengeluaranSubmitted($pengeluaran));
        }

        return $pengeluaran;
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        if ($pengeluaran->status > 0) {
            return response(['message' => 'Pengeluaran sudah di submit.'], 500);
        }

        $pengeluaran->items()->delete();
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
