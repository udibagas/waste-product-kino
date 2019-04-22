<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockBb extends Model
{
    protected $fillable = [
        'kategori_barang_id', 'lokasi', 'qty', 'stock', 'unit', 'location_id'
    ];

    protected $with = ['kategori', 'location'];

    public function kategori() {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }
}
