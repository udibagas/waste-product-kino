<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function bb(Request $request)
    {
        if (!$request->start || !$request->end || !$request->location_id) {
            return [];
        }

        return DB::select( "SELECT
                kategori_barang_id AS kategori_id,
                SUM(qty_in) AS qty_in,
                SUM(qty_out) AS qty_out,
                SUM(stock_in) AS stock_in,
                SUM(stock_out) AS stock_out
            FROM in_out_stock_bbs
            WHERE tanggal BETWEEN :start AND :end
                AND location_id = :location_id
            GROUP BY(kategori_barang_id)",
            [
                ':start' => $request->start,
                ':end' => $request->end,
                ':location_id' => $request->location_id,
            ]
        );
    }

    public function wp(Request $request)
    {
        if (!$request->start || !$request->end || !$request->plant) {
            return [];
        }

        return DB::select( "SELECT
                material,
                material_description,
                SUM(qty_in) AS qty_in,
                SUM(qty_out) AS qty_out,
                SUM(stock_in) AS stock_in,
                SUM(stock_out) AS stock_out
            FROM in_out_stock_wps
            WHERE tanggal BETWEEN :start AND :end
                AND plant = :plant
            GROUP BY material, material_description",
            [
                ':start' => $request->start,
                ':end' => $request->end,
                ':plant' => $request->plant,
            ]
        );
    }
}
