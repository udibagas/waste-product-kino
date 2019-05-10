<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    const STATUS_DRAFT = 0;

    const STATUS_SUBMITTED = 1;

    const STATUS_RECEIVED = 2;

    protected $fillable = [
        'no_sj', 'tanggal', 'penerima', 'status',
        'lokasi_asal', 'lokasi_terima', 'user_id', 'jembatan_timbang',
        'lokasi_asal_id', 'lokasi_terima_id'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'lokasi_asal_id' => 'integer',
        'lokasi_terima_id' => 'integer',
        'status' => 'integer',
    ];

    protected $with = ['items'];

    public function items()
    {
        return $this->hasMany(PengeluaranItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
