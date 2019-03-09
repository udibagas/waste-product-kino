<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PengajuanPenjualanRequest;
use App\PengajuanPenjualan;
use App\PengajuanPenjualanItem;
use App\Events\PengajuanPenjualanSubmitted;

class PengajuanPenjualanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return PengajuanPenjualan::when($request->keyword, function ($q) use ($request) {
            return $q->where('no_sj', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('lokasi_asal', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('lokasi_terima', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('penerima', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function store(PengajuanPenjualanRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $PengajuanPenjualan = PengajuanPenjualan::create($input);
        $PengajuanPenjualan->items()->createMany($request->items);

        if ($request->status == 1) {
            event(new PengajuanPenjualanSubmitted($PengajuanPenjualan, 'PengajuanPenjualan'));
        }

        return $PengajuanPenjualan->with(['items'])->get();
    }

    public function update(PengajuanPenjualanRequest $request, PengajuanPenjualan $PengajuanPenjualan)
    {
        $PengajuanPenjualan->update($request->all());

        foreach ($request->items as $i) 
        {
            if (isset($i['id'])) {
                PengajuanPenjualanItem::find($i['id'])->update($i);
            } else {
                $PengajuanPenjualan->items()->create($i);
            }
        }

        if ($request->status == 1) {
            event(new PengajuanPenjualanSubmitted($PengajuanPenjualan, 'PengajuanPenjualan'));
        }

        return $PengajuanPenjualan;
    }

    public function destroy(PengajuanPenjualan $PengajuanPenjualan)
    {
        if ($PengajuanPenjualan->status > 0) {
            return response(['message' => 'PengajuanPenjualan sudah di submit.'], 500);
        }

        $PengajuanPenjualan->items()->delete();
        $PengajuanPenjualan->delete();
        return ['message' => 'ok'];
    }

    public function getList()
    {
        return PengajuanPenjualan::where('status', 1)->get();
    }
}
