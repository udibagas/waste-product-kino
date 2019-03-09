<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SkemaApprovalPenjualan extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = ['plant', 'lokasi', 'level', 'user_id'];

    protected $with = ['user'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
