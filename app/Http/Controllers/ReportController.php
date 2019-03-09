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
        if (!$request->start || !$request->end || !$request->lokasi) {
            return [];
        }

        return DB::select( "SELECT 
                kategori_barangs.nama AS kategori, 
                SUM(in_out_stock_bbs.stock_in) AS stock_in, 
                SUM(in_out_stock_bbs.stock_out) AS stock_out 
            FROM in_out_stock_bbs 
            JOIN kategori_barangs ON kategori_barangs.id = in_out_stock_bbs.kategori_barang_id
            WHERE in_out_stock_bbs.tanggal BETWEEN :start AND :end
                AND in_out_stock_bbs.lokasi_asal_id = :lokasi
            GROUP BY(kategori_barangs.nama)", 
            [
                ':start' => $request->start,
                ':end' => $request->end,
                ':lokasi' => $request->lokasi,
            ]
        );
    }
}
