<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pembayaran;
use App\Http\Requests\PembayaranRequest;
use App\Penjualan;

class PembayaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(PembayaranRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $input['status'] = 1;
        $pembayaran = Pembayaran::create($input);

        if ($pembayaran->status == Pembayaran::STATUS_SUBMITTED) {
            // kalau jumlah sama settled, kalau kurang partial
            if ($pembayaran->penjualan->terbayar < $pembayaran->penjualan->value) {
                $pembayaran->penjualan->update([
                    'status_pembayaran' => Penjualan::STATUS_PEMBAYARAN_PARTIAL
                ]);
            }

            if ($pembayaran->penjualan->terbayar == $pembayaran->penjualan->value) {
                $pembayaran->penjualan->update([
                    'status_pembayaran' => Penjualan::STATUS_PEMBAYARAN_PAID
                ]);
            }
        }

        return $pembayaran;
    }

    public function update(Pembayaran $pembayaran, PembayaranRequest $request)
    {
        $pembayaran->update($request->all());
        return $pembayaran;
    }

    public function destroy(Pembayaran $pembayaran)
    {
        $pembayaran->delete();
        return ['message' => 'ok'];
    }
}
