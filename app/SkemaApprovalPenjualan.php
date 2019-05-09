<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SkemaApprovalPenjualan extends Model
{
    protected $fillable = ['level', 'user_id', 'location_id'];

    protected $casts = [
        'user_id' => 'integer',
        'location_id' => 'integer',
        'level' => 'integer'
    ];


    protected $with = ['user', 'location'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }
}
