<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    const STATUS_DRAFT = 0;

    const STATUS_SUBMITTED = 1;
    
    protected $fillable = [
        'tanggal', 'no_sj_keluar', 'penerima', 'keterangan',
        'lokasi_asal', 'lokasi_terima', 'user_id', 'status',
        'lokasi_asal_id', 'lokasi_terima_id'
    ];

    protected $with = ['items'];

    public function items()
    {
        return $this->hasMany(PenerimaanItem::class);
    }

    public function pengeluaran()
    {
        return $this->hasOne(Pengeluaran::class, 'no_sj', 'no_sj_keluar');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
