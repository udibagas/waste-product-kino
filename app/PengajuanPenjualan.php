<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengajuanPenjualan extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'tanggal', 'no_aju', 'plant', 'period_from', 'period_to',
        'jenis', 'mvt_type', 'sloc', 'user_id'
    ];
}
