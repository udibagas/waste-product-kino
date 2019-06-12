<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockBb;
use App\Location;
use App\KategoriBarang;

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
            ->where('locations.is_dummy', 0)
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

    public function getStockList(Request $request)
    {
        // kalau lokasi adalah dummy tampilkan semua kategory
        if ($request->location_id) {
            $location = Location::find($request->location_id);
            if ($location->is_dummy) {
                $data = KategoriBarang::where('jenis', 'BB')->get()->toArray();

                return array_map(function($e) use ($request) {
                    $newData = new \stdClass();
                    $newData->kategori = $e;
                    $newData->kategori_barang_id = $e['id'];
                    $newData->stock = 0;
                    $newData->qty = 0;
                    $newData->unit = $e['unit'];
                    $newData->location_id = $request->location_id;
                    return $newData;
                }, $data);
            }
        }

        return StockBb::where('stock', '>', 0)
        ->when($request->location_id, function($q) use ($request) {
            return $q->where('location_id', $request->location_id);
        })->get();
    }
}
