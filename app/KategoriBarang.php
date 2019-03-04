<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'jenis', 'kode', 'nama', 'unit', 'harga', 
        'status', 'created_by', 'updated_by', 'approved_by'
    ];
}
