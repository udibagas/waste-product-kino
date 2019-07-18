<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InOutStockBb;

class InOutStockBbController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return InOutStockBb::selectRaw('in_out_stock_bbs.*')
            ->join('locations', 'locations.id', '=', 'location_id')
            ->join('kategori_barangs', 'kategori_barangs.id', '=', 'kategori_barang_id')
            ->when($request->range, function($q) use ($request) {
                return $q->whereBetween('tanggal', $request->range);
            })->when($request->keyword, function ($q) use ($request) {
                return $q->where('no_sj', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('locations.name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('kategori_barangs.nama', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('kategori_barangs.kode', 'LIKE', '%' . $request->keyword . '%');
            })->when($request->location_id, function ($q) use ($request) {
                return $q->whereIn('location_id', $request->location_id);
            })->when($request->lokasi_asal, function ($q) use ($request) {
                return $q->whereIn('lokasi_asal', $request->lokasi_asal);
            })->when($request->kategori_barang_id, function ($q) use ($request) {
                return $q->whereIn('kategori_barang_id', $request->kategori_barang_id);
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }
}
