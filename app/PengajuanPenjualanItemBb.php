<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengajuanPenjualanItemBb extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'pengajuan_penjualan_id', 'kategori_barang_id', 'jumlah', 
        'jumlah_terima', 'eun', 'timbangan_manual'
    ];

    protected $with = ['kategori'];

    public function kategori() {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }
}
