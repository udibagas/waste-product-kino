<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PenerimaanRequest;
use App\Penerimaan;
use App\PenerimaanItem;
use App\Events\PenerimaanSubmitted;

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

        return Penerimaan::when($request->keyword, function ($q) use ($request) {
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
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $penerimaan = Penerimaan::create($input);
        $penerimaan->items()->createMany($request->items);

        if ($request->status == Penerimaan::STATUS_SUBMITTED) {
            event(new PenerimaanSubmitted($penerimaan));
        }

        return $penerimaan;
    }

    public function update(PenerimaanRequest $request, Penerimaan $penerimaan)
    {
        $penerimaan->update($request->all());

        foreach ($request->items as $i) {
            PenerimaanItem::find($i['id'])->update($i);
        }

        if ($request->status == Penerimaan::STATUS_SUBMITTED) {
            event(new PenerimaanSubmitted($penerimaan));
        }

        return $penerimaan;
    }

    public function destroy(Penerimaan $penerimaan)
    {
        if ($penerimaan->status > 0) {
            return response(['message' => 'Penerimaan sudah di submit.'], 500);
        }

        $penerimaan->items()->delete();
        $penerimaan->delete();
        return ['message' => 'ok'];
    }
}
