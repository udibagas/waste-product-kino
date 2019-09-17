<?php

namespace App\Listeners;

use App\Events\PengeluaranSubmitted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\InOutStockBb;
use App\StockBb;

class UpdateStockBbPengeluaran
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
     * @param  PengeluaranSubmitted  $event
     * @return void
     */
    public function handle(PengeluaranSubmitted $event)
    {
        foreach ($event->pengeluaran->items as $item)
        {
            InOutStockBb::create([
                'tanggal' => $event->pengeluaran->tanggal,
                'lokasi_asal' => '',
                'lokasi_asal_id' => '',
                'location_id' => $event->pengeluaran->lokasi_asal_id,
                'kategori_barang_id' => $item->kategori_barang_id,
                'eun' => $item->eun,
                'stock_in' => 0,
                'stock_out' => $item->timbangan_manual,
                'no_sj' => $event->pengeluaran->no_sj
            ]);

            $stock = StockBb::where('location_id', $event->pengeluaran->lokasi_asal_id)
                ->where( 'kategori_barang_id', $item->kategori_barang_id)
                ->first();

            if ($stock) {
                $stock->stock = $stock->stock - $item->timbangan_manual;
                $stock->save();
            }

            else {
                StockBb::create([
                    'kategori_barang_id' => $item->kategori_barang_id,
                    'location_id' => $event->pengeluaran->lokasi_asal_id,
                    'lokasi' => $event->pengeluaran->lokasi_asal,
                    'stock' => 0,
                    'unit' => $item->eun
                ]);
            }
        }
    }
}
