<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenjualanItemBb extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'kategori_barang_id', 'qty', 'jembatan_timbang', 'price', 'timbangan_manual',
        'price_per_kg', 'value', 'keterangan', 'penjualan_id',
        'stock_berat', 'stock_qty'
    ];

    protected $with = ['kategori'];

    public function kategori() {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }
}
