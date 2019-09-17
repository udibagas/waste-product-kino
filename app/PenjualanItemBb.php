<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenjualanItemBb extends Model
{
    // NOTE : jembatan_timbang = jumlah aktual dijual
    protected $fillable = [
        'kategori_barang_id', 'timbangan_manual',
        'price_per_kg', 'value', 'penjualan_id',
        'stock_berat', 'jembatan_timbang'
    ];

    protected $casts = [
        'penjualan_id' => 'integer',
        'kategori_barang_id' => 'integer',
        'jembatan_timbang' => 'float'
    ];

    protected $with = ['kategori'];

    public function kategori() {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }
}
