<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

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
}
