<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InOutStockBb extends Model
{
    protected $fillable = [
        'tanggal', 'lokasi_asal', 'kategori_barang_id', 'location_id',
        'eun', 'stock_in', 'stock_out', 'no_sj', 'lokasi_asal_id',
        'qty_in', 'qty_out'
    ];

    protected $with = ['barang', 'location'];

    public function barang() {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }
}
