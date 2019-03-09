<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KonversiBerat extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'kategori_jual', 'finished_good', 'material_id', 
        'material_description', 'berat', 'keterangan'
    ];
}
