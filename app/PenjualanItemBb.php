<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenjualanItemBb extends Model
{
    protected $fillable = [
        'kategori_barang_id', 'qty', 'jembatan_timbang', 'price', 'timbangan_manual',
        'price_per_kg', 'value', 'keterangan', 'penjualan_id',
        'stock_berat', 'stock_qty'
    ];

    protected $casts = [
        'penjualan_id' => 'integer',
        'kategori_barang_id' => 'integer',
        'qty' => 'integer',
        'stock_qty' => 'integer',
    ];

    protected $with = ['kategori'];

    public function kategori() {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }
}
