<?php

namespace App\Listeners;

use App\Events\PenjualanSubmitted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\InOutStockBb;
use App\StockBb;
use DB;
use App\PengajuanPenjualan;

class UpdateStockPenjualan
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PenjualanSubmitted  $event
     * @return void
     */
    public function handle(PenjualanSubmitted $event)
    {
        // BB ga ada penjualan partial
        if ($event->penjualan->jenis == 'BB') {
            DB::update("UPDATE pengajuan_penjualans SET status = :status WHERE no_aju = :no_aju", [
                ':status' => PengajuanPenjualan::STATUS_PROCESSED,
                ':no_aju' => $event->penjualan->no_aju
            ]);
        }

        if ($event->penjualan->jenis == 'BB')
        {
            foreach ($event->penjualan->itemsBb as $item)
            {
                InOutStockBb::create([
                    'tanggal' => $event->penjualan->tanggal,
                    'lokasi_asal' => '',
                    'lokasi_asal_id' => '',
                    'location_id' => $event->penjualan->location_id,
                    'kategori_barang_id' => $item->kategori_barang_id,
                    'eun' => $item->kategori->unit,
                    'stock_in' => 0,
                    'stock_out' => $item->jembatan_timbang,
                    'no_sj' => $event->penjualan->no_sj
                ]);

                $stock = StockBb::where('location_id', $event->penjualan->location_id)
                    ->where('kategori_barang_id', $item->kategori_barang_id)
                    ->first();

                if ($stock) {
                    $stock->stock = $stock->stock - $item->jembatan_timbang;
                    $stock->save();
                } else {
                    StockBb::create([
                        'kategori_barang_id' => $item->kategori_barang_id,
                        'location_id' => $event->penjualan->location_id,
                        'lokasi' => $event->penjualan->location->name,
                        'stock' => 0,
                        'unit' => $item->kategori->unit,
                    ]);
                }
            }
        }

        if ($event->penjualan->jenis == 'WP')
        {
            foreach($event->penjualan->itemsWp as $item)
            {
                DB::update('UPDATE stock_wps SET stock -= :berat WHERE plant = :plant AND material = :material', [
                    ':berat' => $item->berat_dijual * 1000,
                    ':plant' => $event->penjualan->location->plant,
                    'material' => $item->material_id
                ]);

                // update terjual di item pengajuan
                DB::update('UPDATE pengajuan_penjualan_item_wps SET terjual += :terjual WHERE material_id = :material_id AND pengajuan_penjualan_id = :id', [
                    ':id' => $event->penjualan->pengajuan->id,
                    ':terjual' => $item->berat_dijual,
                    ':material_id' => $item->material_id
                ]);

                DB::table('in_out_stock_wps')->insert([
                    'tanggal' => date('Y-m-d'),
                    'no_aju' => $event->penjualan->no_aju,
                    'no_sj' => $event->penjualan->no_sj,
                    'material' => $item->material_id,
                    'material_description' => $item->material_description,
                    'stock_out' => $item->berat_dijual,
                    'plant' => $event->penjualan->location->plant,
                    'sloc' => '',
                    'mvt' => '',
                ]);
            }

            // berat penjualan lebih besar atau sama dengan pengajuan

            $sql1 = "SELECT SUM(berat) AS [total_berat]
            FROM pengajuan_penjualan_item_wps
            JOIN pengajuan_penjualans
                ON pengajuan_penjualans.id = pengajuan_penjualan_item_wps.pengajuan_penjualan_id
            WHERE pengajuan_penjualans.no_aju = :no_aju";

            // yang sudah di submit
            $sql2 = "SELECT SUM(berat_dijual) AS [total_berat]
            FROM penjualan_item_wps
            JOIN penjualans
                ON penjualans.id = penjualan_item_wps.penjualan_id
            WHERE penjualans.no_aju = :no_aju
                AND penjualans.status = 1";

            $pengajuan = DB::select($sql1, [':no_aju' => $event->penjualan->no_aju])[0]->total_berat;
            $penjualan = DB::select($sql2, [':no_aju' => $event->penjualan->no_aju])[0]->total_berat;

            DB::update("UPDATE pengajuan_penjualans SET status = :status WHERE no_aju = :no_aju", [
                ':status' => $penjualan >= $pengajuan ? PengajuanPenjualan::STATUS_PROCESSED : PengajuanPenjualan::STATUS_PARTIALLY_PROCESSED,
                ':no_aju' => $event->penjualan->no_aju
            ]);
        }
    }
}
