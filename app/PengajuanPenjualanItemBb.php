<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengajuanPenjualanItemBb extends Model
{
    protected $fillable = [
        'pengajuan_penjualan_id', 'kategori_barang_id', 'jumlah', 
        'eun', 'timbangan_manual', 'stock_berat', 'stock_qty'
    ];

    protected $with = ['kategori'];

    public function kategori() {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }
}
