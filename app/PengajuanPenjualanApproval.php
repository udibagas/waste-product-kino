<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengajuanPenjualanApproval extends Model
{
    protected $fillable = ['pengajuan_penjualan_id', 'user_id', 'status', 'level', 'note'];

    protected $casts = [
        'pengajuan_penjualan_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'integer',
        'level' => 'integer'
    ];

    protected $with = ['user'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
