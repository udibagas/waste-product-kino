<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenjualanItemWp extends Model
{
    protected $fillable = [
        'penjualan_id', 'material_id', 'material_description',
        'price_per_unit', 'value', 'berat', 'kategori', 'berat_dijual', 'terjual'
    ];

    protected $casts = [
        'penjualan_id' => 'integer',
        'price_per_unit' => 'integer',
        'value' => 'integer',
        'berat' => 'float',
        'terjual' => 'float',
        'berat_dijual' => 'float',
    ];
}
