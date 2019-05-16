<?php

namespace App\Listeners;

use App\Events\PenerimaanSubmitted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\InOutStockBb;
use App\Pengeluaran;
use App\StockBb;

class UpdateStockBbPenerimaan
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
     * @param  PenerimaanSubmitted  $event
     * @return void
     */
    public function handle(PenerimaanSubmitted $event)
    {
        Pengeluaran::where('no_sj', $event->penerimaan->no_sj_keluar)->update([
            'status' => 2,
            'penerima' => $event->penerimaan->penerima
        ]);

        foreach ($event->penerimaan->items as $item)
        {
            InOutStockBb::create([
                'tanggal' => $event->penerimaan->tanggal,
                'lokasi_asal' => $event->penerimaan->lokasi_asal,
                'lokasi_asal_id' => $event->penerimaan->lokasi_asal_id,
                'location_id' => $event->penerimaan->lokasi_terima_id,
                'kategori_barang_id' => $item->kategori_barang_id,
                'eun' => $item->eun,
                'stock_out' => 0,
                'stock_in' => $item->timbangan_manual_terima,
                'qty_out' => 0,
                'qty_in' => $item->qty_terima,
                'no_sj' => $event->penerimaan->no_sj_keluar
            ]);

            $stock = StockBb::where('location_id', $event->penerimaan->lokasi_terima_id)
                ->where('kategori_barang_id', $item->kategori_barang_id)
                ->first();

            if ($stock) {
                $stock->stock = $stock->stock + $item->timbangan_manual_terima;
                $stock->qty = $stock->qty + $item->qty_terima;
                $stock->save();
            } else {
                StockBb::create([
                    'kategori_barang_id' => $item->kategori_barang_id,
                    'location_id' => $event->penerimaan->lokasi_terima_id,
                    'lokasi' => $event->penerimaan->lokasi_terima,
                    'stock' => $item->timbangan_manual_terima,
                    'qty' => $item->qty_terima,
                    'unit' => $item->eun
                ]);
            }
        }
    }
}
