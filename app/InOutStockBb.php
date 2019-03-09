<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InOutStockBb extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'tanggal', 'lokasi_asal', 'lokasi_terima', 'kategori_barang_id',
        'eun', 'stock_in', 'stock_out', 'no_sj', 'lokasi_asal_id', 'lokasi_terima_id'
    ];

    protected $with = ['barang'];

    public function barang() {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }
}
