<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name', 'plant', 'is_dummy'];

    protected $casts = ['is_dummy' => 'boolean'];

    public function skemaApprovalPenjualan()
    {
        return $this->hasMany(SkemaApprovalPenjualan::class);
    }
}
