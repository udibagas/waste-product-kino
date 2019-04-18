<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PenjualanRequest;
use App\Penjualan;
use App\PenjualanItem;
use App\Events\PenjualanSubmitted;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return Penjualan::when($request->keyword, function ($q) use ($request) {
            return $q->where('no_sj', 'LIKE', '%' . $request->keyword . '%');
        })->when($request->pembeli_id, function ($q) use ($request) {
            return $q->whereIn('pembeli_id', $request->pembeli_id);
        })->when($request->location_id, function ($q) use ($request) {
            return $q->whereIn('location_id', $request->location_id);
        })->when($request->status, function ($q) use ($request) {
            return $q->whereIn('status', $request->status);
        })->when($request->user()->role == \App\User::ROLE_USER, function ($q) use ($request) {
            return $q->where('location_id', $request->user()->location_id);
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function store(PenjualanRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $penjualan = Penjualan::create($input);

        if ($request->jenis == 'BB') {
            $penjualan->itemsBb()->createMany($request->items_bb);
        }

        if ($request->jenis == 'WP') {
            $penjualan->itemsWp()->createMany($request->items_wp);
        }

        if ($request->status == Penjualan::STATUS_SUBMITTED) {
            event(new PenjualanSubmitted($penjualan));
        }

        return $penjualan;
    }

    public function update(PenjualanRequest $request, Penjualan $penjualan)
    {
        $penjualan->update($request->all());

        foreach ($request->items as $i) {
            PenjualanItem::find($i['id'])->update($i);
        }

        if ($request->status == Penjualan::STATUS_SUBMITTED) {
            event(new PenjualanSubmitted($penjualan));
        }

        return $penjualan;
    }

    public function destroy(Penjualan $penjualan)
    {
        if ($penjualan->status > 0) {
            return response(['message' => 'Penjualan sudah di submit.'], 500);
        }

        $penjualan->itemsBb()->delete();
        // $penjualan->itemsWp()->delete();
        $penjualan->delete();
        return ['message' => 'ok'];
    }
}
