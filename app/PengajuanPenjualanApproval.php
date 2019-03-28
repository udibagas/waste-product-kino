<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengajuanPenjualanApproval extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = ['pengajuan_penjualan_id', 'user_id', 'status', 'level', 'note'];
}
