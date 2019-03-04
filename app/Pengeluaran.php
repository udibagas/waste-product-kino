<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'no_sj', 'tanggal', 'penerima',
        'lokasi_asal', 'lokasi_terima', 'user_id', 'jembatan_timbang'
    ];

    public function items()
    {
        return $this->hasMany(PengaluaranItem::class);
    }
}
