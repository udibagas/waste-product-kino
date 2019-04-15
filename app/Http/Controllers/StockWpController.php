<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockWp;
use App\KonversiBerat;
use Illuminate\Support\Facades\DB;

class StockWpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'plant';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return StockWp::when($request->keyword, function ($q) use ($request) {
            return $q->where('material', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('material_description', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('plant', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('sloc', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('mvt', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('mat_doc', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function getStock(Request $request)
    {

    }

    public function store(Request $request)
    {
        try 
        {
            DB::beginTransaction();
            foreach ($request->rows as $i => $row) 
            {
                $stock = StockWp::where('plant', $row['plant'])
                    ->where('material', $row['material'])
                    ->first();
                
                // cari konversi
                $konversi = KonversiBerat::where('material_id', $row['material'])->first();

                if ($stock) {
                    if ($konversi) {
                        $row['stock'] = $stock->stock + ($stock->quantity * $konversi->berat);
                    }

                    $row['quantity'] += $stock->quantity;
                    DB::table('stock_wps')->where('id', $stock->id)->update($row);
                } else {
                    $row['quantity'] = $row['quantity'] == null ? 0 : $row['quantity'];
                    
                    if ($konversi) {
                        $row['stock'] = $row['quantity'] * $konversi->berat;
                    }

                    DB::table('stock_wps')->insert($row);
                }
            }

            DB::commit();
            return 'Data imported successfully. You can close this window or uplad more files.<br>';
        } catch (\Exception $e) {
            DB::rollBack();
            return '[ERROR] Failed to import data. ' . $e->getMessage().'<br>';
        }
    }

    public function getSlocList()
    {
        $data = DB::select("SELECT DISTINCT(sloc) FROM stock_wps");
        return array_map(function($d) { 
            return $d->sloc; 
        }, $data);
    }

    public function getMvtList()
    {
        $data = DB::select("SELECT DISTINCT(mvt) FROM stock_wps");
        return array_map(function ($d) {
            return $d->mvt;
        }, $data);
    }

    public function getList(Request $request)
    {
        return StockWp::when($request->plant, function ($q) use ($request) {
            return $q->where('plant', $request->plant);
        })->when($request->mvt_type, function ($q) use ($request) {
            return $q->whereIn('mvt', $request->mvt_type);
        })->when($request->sloc, function ($q) use ($request) {
            return $q->where('sloc', $request->sloc);
        })->orderBy('material', 'asc')->get(); 
    }
}
