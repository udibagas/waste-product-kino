<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Penjualan extends Model
{
    const STATUS_DRAFT = 0;

    const STATUS_SUBMITTED = 1;

    const STATUS_PEMBAYARAN_NO_PAYMENT = 0;

    const STATUS_PEMBAYARAN_PARTIAL = 1;

    const STATUS_PEMBAYARAN_PAID = 2;

    protected $fillable = [
        'tanggal', 'no_aju', 'pembeli_id', 'no_sj', 'value',
        'top_date', 'jembatan_timbang', 'user_id', 'status',
        'jenis', 'location_id', 'status_pembayaran'
    ];

    protected $casts = [
        'pembeli_id' => 'integer',
        'user_id' => 'integer',
        'location_id' => 'integer',
        'status' => 'integer'
    ];

    protected $with = ['itemsBb', 'itemsWp', 'location', 'pembeli', 'pembayaran'];

    protected $appends = ['terbayar'];

    public function itemsBb() {
        return $this->hasMany(PenjualanItemBb::class);
    }

    public function itemsWp() {
        return $this->hasMany(PenjualanItemWp::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function pembeli() {
        return $this->belongsTo(Pembeli::class);
    }

    public function pembayaran() {
        return $this->hasMany(Pembayaran::class);
    }

    public function getPengajuanAttribute() {
        $sql = "SELECT * FROM pengajuan_penjualans WHERE no_aju = ?";
        return DB::select($sql, [$this->no_aju])[0];
    }

    public function getTerbayarAttribute() {
        $sql = "SELECT SUM(value) AS terbayar FROM pembayarans WHERE penjualan_id = ?";
        $terbayar = DB::SELECT($sql, [$this->id])[0]->terbayar;
        return $terbayar ? $terbayar : 0;
    }

    public function getApprover1Attribute() {
        return 'a';
    }

    public function getApprover2Attribute() {
        return 'a';
    }
}
