<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    const STATUS_DRAFT = 0;

    const STATUS_SUBMITTED = 1;
    
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'tanggal', 'no_aju', 'pembeli_id', 'no_sj', 'value', 
        'top_date', 'jembatan_timbang', 'user_id', 'status',
        'jenis', 'location_id'
    ];

    protected $with = ['itemsBb', 'location', 'pembeli'];

    public function itemsBb() {
        return $this->hasMany(PenjualanItemBb::class);
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function pembeli() {
        return $this->belongsTo(Pembeli::class);
    }
}
