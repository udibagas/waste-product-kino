<?php

namespace App\Listeners;

use App\Events\PenjualanSubmitted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\InOutStockBb;
use App\StockBb;
use DB;
use App\PengajuanPenjualan;

class UpdateStockBbPenjualan
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
        DB::update("UPDATE pengajuan_penjualans SET status = :status WHERE no_aju = :no_aju", [
            ':status' => PengajuanPenjualan::STATUS_PROCESSED,
            ':no_aju' => $event->penjualan->no_aju
        ]);

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
                    'stock_out' => $item->timbangan_manual,
                    'qty_in' => 0,
                    'qty_out' => $item->qty,
                    'no_sj' => $event->penjualan->no_sj
                ]);

                $stock = StockBb::where('location_id', $event->penjualan->location_id)
                    ->where('kategori_barang_id', $item->kategori_barang_id)
                    ->first();

                if ($stock) {
                    $stock->stock = $stock->stock - $item->timbangan_manual;
                    $stock->qty = $stock->qty - $item->qty;
                    $stock->save();
                } else {
                    StockBb::create([
                        'kategori_barang_id' => $item->kategori_barang_id,
                        'location_id' => $event->penjualan->location_id,
                        'lokasi' => $event->penjualan->location->name,
                        'qty' => 0,
                        'stock' => 0,
                        'unit' => $item->kategori->unit,
                    ]);
                }
            }
        }

        // if ($event->penjualan->jenis == 'WP')
        // {
        //     foreach($event->penjualan->itemsWp as $item)
        //     {

        //     }
        // }
    }
}
