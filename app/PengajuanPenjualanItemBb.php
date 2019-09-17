<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengajuanPenjualanItemBb extends Model
{
    protected $fillable = [
        'pengajuan_penjualan_id', 'kategori_barang_id',
        'eun', 'timbangan_manual', 'stock_berat',
    ];

    protected $casts = [
        'pengajuan_penjualan_id' => 'integer',
        'kategori_barang_id' => 'integer',
        'stock_berat' => 'float'
    ];

    protected $with = ['kategori'];

    public function kategori() {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }
}
