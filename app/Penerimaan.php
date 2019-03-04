<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penerimaan extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'no_sj', 'tanggal', 'no_sj_keluar', 'penerima',
        'lokasi_asal', 'lokasi_terima', 'user_id'
    ];

    public function items()
    {
        return $this->hasMany(PenerimaanItem::class);
    }
}
