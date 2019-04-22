<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $fillable = [
        'jenis', 'kode', 'nama', 'unit', 'harga', 
        'status', 'created_by', 'updated_by', 'approved_by'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
