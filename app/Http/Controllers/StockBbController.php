<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockBb;

class StockBbController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'lokasi';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return StockBb::join('kategori_barangs', 'kategori_barangs.id', '=', 'stock_bbs.kategori_barang_id')
        ->when($request->keyword, function ($q) use ($request) {
            return $q->where('kategori_barangs.nama', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere( 'stock_bbs.plant', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere( 'stock_bbs.lokasi', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }
}
