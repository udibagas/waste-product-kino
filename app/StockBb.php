<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockBb extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'kategori_barang_id', 'lokasi', 'stock', 'unit', 'plant', 'location_id'
    ];

    protected $with = ['barang', 'location'];

    public function barang() {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }
}
