<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Pembayaran extends Model
{
    const STATUS_DRAFT = 0;

    const STATUS_SUBMITTED = 1;

    protected $fillable = ['penjualan_id', 'tanggal', 'value', 'user_id', 'status', 'keterangan'];

    protected $appends = ['user'];

    protected $casts = [
        'penjualan_id' => 'integer',
        'user_id' => 'integer'
    ];

    public function penjualan() {
        return $this->belongsTo(Penjualan::class);
    }

    public function getUserAttribute() {
        $sql = "SELECT name FROM users WHERE id = ?";
        return DB::select($sql, [$this->user_id])[0]->name;
    }
}
