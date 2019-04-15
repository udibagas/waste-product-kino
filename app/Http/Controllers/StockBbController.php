<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockBb;

class StockBbController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'locations.plant';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return StockBb::selectRaw('stock_bbs.*')
            ->join('kategori_barangs', 'kategori_barangs.id', '=', 'stock_bbs.kategori_barang_id')
            ->join('locations', 'locations.id', '=', 'stock_bbs.location_id')
            ->when($request->location_id, function($q) use ($request) {
                return $q->whereIn('location_id', $request->location_id);
            })->when($request->kategori_barang_id, function($q) use ($request) {
                return $q->whereIn('kategori_barang_id', $request->kategori_barang_id);
            })->when($request->keyword, function ($q) use ($request) {
                return $q->where('kategori_barangs.nama', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere( 'locations.plant', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere( 'locations.name', 'LIKE', '%' . $request->keyword . '%');
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function getStock(Request $request)
    {
        return StockBb::where('location_id', $request->location_id)
            ->where( 'kategori_barang_id', $request->kategori_barang_id)
            ->first();
    }
}
