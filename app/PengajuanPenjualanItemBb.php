<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengajuanPenjualanItemBb extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'kategori_barang_id', 'jumlah', 'jumlah_terima', 'eun', 'timbangan_manual'
    ];
}
