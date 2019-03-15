<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengajuanPenjualan extends Model
{
    const STATUS_DRAFT = 0;

    const STATUS_SUBMITTED = 1;

    const STATUS_APPROVED = 2;

    const STATUS_REJECTED = 3;

    const STATUS_APPROVAL_PENDING = 0;

    const STATUS_APPROVAL_APPROVED = 1;

    const STATUS_APPROVAL_REJECTED = 2;

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'tanggal', 'no_aju', 'plant', 'period_from', 'period_to',
        'jenis', 'mvt_type', 'sloc', 'user_id', 'location_id', 'status',
        'approval1_status', 'approval1_time', 'approval1_by',
        'approval2_status', 'approval2_time', 'approval2_by'
    ];

    protected $with = ['itemsBb', 'itemsWp', 'location', 'user'];

    public function itemsBb()
    {
        return $this->hasMany(PengajuanPenjualanItemBb::class);
    }

    public function itemsWp()
    {
        return $this->hasMany(PengajuanPenjualanItemWp::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
