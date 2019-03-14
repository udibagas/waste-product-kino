<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'tanggal', 'no_aju', 'pembeli_id', 'no_sj', 'value', 'top_date',
        'jembatan_timbang', 'user_id', 'status'
    ];
}
