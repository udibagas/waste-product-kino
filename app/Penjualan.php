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
    
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'tanggal', 'no_aju', 'pembeli_id', 'no_sj', 'value', 
        'top_date', 'jembatan_timbang', 'user_id', 'status',
        'jenis', 'location_id', 'status_pembayaran'
    ];

    protected $with = ['itemsBb', 'location', 'pembeli', 'pembayaran'];

    protected $appends = ['terbayar'];

    public function itemsBb() {
        return $this->hasMany(PenjualanItemBb::class);
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

    public function getTerbayarAttribute() {
        $sql = "SELECT SUM(value) AS terbayar FROM pembayarans WHERE penjualan_id = ?";
        $terbayar = DB::SELECT($sql, [$this->id])[0]->terbayar;
        return $terbayar ? $terbayar : 0;
    }
}
