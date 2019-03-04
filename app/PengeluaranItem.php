<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengeluaranItem extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'pengeluaran_id', 'kategori_barang_id', 'qty',
        'eun', 'timbangan_manual'
    ];
}
