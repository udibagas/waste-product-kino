<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Pembayaran extends Model
{
    const STATUS_DRAFT = 0;

    const STATUS_SUBMITTED = 1;

    protected $dateFormat = "Y-m-d H:i:s";

    protected $fillable = ['penjualan_id', 'tanggal', 'value', 'user_id', 'status', 'keterangan'];

    protected $appends = ['user'];

    public function penjualan() {
        return $this->belongsTo(Penjualan::class);
    }

    public function getUserAttribute() {
        $sql = "SELECT name FROM users WHERE id = ?";
        return DB::select($sql, [$this->user_id])[0]->name;
    }
}
