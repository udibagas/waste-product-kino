<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PengajuanPenjualan extends Model
{
    const STATUS_DRAFT = 0;

    const STATUS_SUBMITTED = 1;

    const STATUS_APPROVED = 2;

    const STATUS_REJECTED = 3;

    const STATUS_PROCESSED = 4;

    const STATUS_APPROVAL_PENDING = 0;

    const STATUS_APPROVAL_APPROVED = 1;

    const STATUS_APPROVAL_REJECTED = 2;

    protected $fillable = [
        'tanggal', 'no_aju', 'period_from', 'period_to',
        'jenis', 'mvt_type', 'sloc', 'user_id', 'location_id', 'status',
        'approval1_status', 'approval1_time', 'approval1_by',
        'approval2_status', 'approval2_time', 'approval2_by'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'location_id' => 'integer',
        'status' => 'integer',
        'approval1_status' => 'integer',
        'approval2_status' => 'integer',
    ];

    protected $with = ['itemsBb', 'itemsWp', 'location', 'user'];

    protected $appends = ['summaryItems'];

    public function itemsBb()
    {
        return $this->hasMany(PengajuanPenjualanItemBb::class);
    }

    public function itemsWp()
    {
        return $this->hasMany(PengajuanPenjualanItemWp::class);
    }

    public function approvals()
    {
        return $this->hasMany(PengajuanPenjualanApproval::class, 'pengajuan_penjualan_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSummaryItemsAttribute()
    {
        $sql = "SELECT
            kategori,
            SUM(stock) / 1000 AS [stock],
            SUM(berat) AS [berat],
            AVG(price_per_unit) AS [price_per_unit],
            (SUM(berat) * AVG(price_per_unit)) AS [value]
        FROM pengajuan_penjualan_item_wps
        WHERE pengajuan_penjualan_id = :id
        GROUP BY kategori";

        return DB::select($sql, [':id' => $this->id]);
    }
}
