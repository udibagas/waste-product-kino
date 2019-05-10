<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PengeluaranItem extends Model
{
    protected $fillable = [
        'pengeluaran_id', 'kategori_barang_id', 'qty',
        'eun', 'timbangan_manual'
    ];

    protected $casts = [
        'pengeluaran_id' => 'integer',
        'kategori_barang_id' => 'integer',
        'qty' => 'integer',
        'timbangan_manual' => 'float'
    ];

    protected $with = ['barang'];

    public function barang() {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }
}
