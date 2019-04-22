<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pembeli extends Model
{
    protected $fillable = ['nama', 'kontak', 'alamat', 'bank', 'nomor_rekening', 'pemegang_rekening'];

}
