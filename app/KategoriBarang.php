<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $fillable = [
        'jenis', 'kode', 'nama', 'unit', 'harga', 'toleransi_penjualan',
        'status', 'created_by', 'updated_by', 'approved_by'
    ];

    protected $casts = [
        'toleransi_penjualan' => 'integer'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
