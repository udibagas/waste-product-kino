<?php

namespace App\Listeners;

use App\Events\PenjualanSubmitted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\InOutStockBb;
use DB;

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
        foreach ($event->penjualan->items as $item) 
        {
            InOutStockBb::create([
                'tanggal' => $event->penjualan->tanggal,
                'lokasi_asal' => $event->penjualan->lokasi_asal,
                'lokasi_terima' => '',
                'kategori_barang_id' => $item->kategori_barang_id,
                'eun' => $item->eun,
                'stock_in' => 0,
                'stock_out' => $item->timbangan_manual, // tanya lagi, atau pakai qty?
                'no_sj' => $event->penjualan->no_sj
            ]);

            // kurangi stock di lokasi_asal
            DB::update("UPDATE stock_bbs SET stock = stock - :jumlah WHERE kategori_barang_id = :kategori AND lokasi = :lokasi", [
                ':jumlah' => $item->timbangan_manual,
                ':kategori' => $item->kategori_barang_id,
                ':lokasi' => $event->penjualan->lokasi_terima
            ]);
        }
    }
}
