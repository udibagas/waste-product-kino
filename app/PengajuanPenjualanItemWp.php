<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengajuanPenjualanItemWp extends Model
{
    protected $fillable = [
        'pengajuan_penjualan_id', 'kategori_barang_id', 'material_id', 'divisi',
        'unit', 'qty_reject', 'price_per_unit', 'value'
    ];

    protected $casts = [
        'pengajuan_penjualan_id' => 'integer',
        'kategori_barang_id' => 'integer',
    ];
}
