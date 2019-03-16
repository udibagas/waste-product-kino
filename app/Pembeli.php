<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pembeli extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';
    
    protected $fillable = ['nama', 'kontak', 'alamat', 'bank', 'nomor_rekening', 'pemegang_rekening'];

}
