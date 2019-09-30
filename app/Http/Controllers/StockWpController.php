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

        return StockWp::selectRaw('stock_wps.*, konversi_berats.kategori_jual AS [kategori], kategori_barangs.harga AS [price_per_unit]')
        ->join('konversi_berats', 'konversi_berats.material_id', '=', 'stock_wps.material')
        ->join('kategori_barangs', 'kategori_barangs.nama', '=', 'konversi_berats.kategori_jual')
        ->where('kategori_barangs.jenis', 'WP')
        ->when($request->keyword, function ($q) use ($request) {
            return $q->where('stock_wps.material', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('stock_wps.material_description', 'LIKE', '%' . $request->keyword . '%');
                // ->orWhere('plant', 'LIKE', '%' . $request->keyword . '%')
                // ->orWhere('sloc', 'LIKE', '%' . $request->keyword . '%')
                // ->orWhere('mvt', 'LIKE', '%' . $request->keyword . '%')
                // ->orWhere('mat_doc', 'LIKE', '%' . $request->keyword . '%');
        })->when($request->kategori, function($q) use ($request) {
            return $q->whereIn('kategori_barangs.nama', $request->kategori);
        })->when($request->plant, function($q) use ($request) {
            return $q->whereIn('plant', $request->plant);
        })->when($request->sloc, function($q) use ($request) {
            return $q->whereIn('sloc', $request->sloc);
        })->when($request->mvt, function($q) use ($request) {
            return $q->whereIn('mvt', $request->mvt);
        })->when($request->mat, function($q) use ($request) {
            return $q->whereIn('mat', $request->mat);
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function getStock(Request $request)
    {

    }

    public function store(Request $request)
    {
        $tanggal = date('Y-m-d');

        try
        {
            DB::beginTransaction();
            foreach ($request->rows as $i => $row)
            {
                // cari konversi
                $konversi = KonversiBerat::where('material_id', $row['material'])->first();

                $row1 = $row;
                unset($row1['stock']);
                unset($row1['quantity']);
                $row1['location_id'] = 0;
                $row1['tanggal'] = $tanggal;

                if ((int) $row['quantity'] >= 0) {
                    $row1['qty_in'] = (int) $row['quantity'];
                    $row1['stock_in'] = $konversi ? $row1['qty_in'] * $konversi->berat : 0;
                } else {
                    $row1['qty_out'] = abs((int) $row['quantity']);
                    $row1['stock_out'] = $konversi ? $row1['qty_out'] * $konversi->berat : 0;
                }

                DB::table('in_out_stock_wps')->insert($row1);

                $stock = StockWp::where('plant', $row['plant'])
                    ->where('material', $row['material'])
                    ->first();

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

    public function getMatList()
    {
        $data = DB::select("SELECT DISTINCT(mat) FROM stock_wps");
        return array_map(function ($d) {
            return $d->mat;
        }, $data);
    }

    public function getList(Request $request)
    {
        return StockWp::selectRaw('
            stock_wps.material AS [material],
            stock_wps.material_description AS [material_description],
            stock_wps.stock AS [stock],
            konversi_berats.kategori_jual AS [kategori],
            kategori_barangs.harga AS [price_per_unit]
        ')
        ->join('konversi_berats', 'konversi_berats.material_id', '=', 'stock_wps.material')
        ->join('kategori_barangs', 'kategori_barangs.nama', '=', 'konversi_berats.kategori_jual')
        ->where('stock_wps.stock', '>', 0)
        ->when($request->plant, function ($q) use ($request) {
            return $q->where('stock_wps.plant', $request->plant);
        })->when($request->mvt_type, function ($q) use ($request) {
            return $q->whereIn('stock_wps.mvt', $request->mvt_type);
        })->when($request->sloc, function ($q) use ($request) {
            return $q->where('stock_wps.sloc', $request->sloc);
        })->orderBy('stock_wps.material', 'asc')->get();
    }
}
