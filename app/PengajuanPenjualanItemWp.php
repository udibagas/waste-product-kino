<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengajuanPenjualanItemWp extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'kategori_barang_id', 'material_id', 'divisi', 
        'unit', 'qty_reject', 'price_per_unit', 'value'
    ];
}
