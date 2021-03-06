<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenerimaanItem extends Model
{
    protected $fillable = [
        'penerimaan_id', 'kategori_barang_id',
        'eun', 'timbangan_manual_kirim',
        'timbangan_manual_terima', 'keterangan'
    ];

    protected $casts = [
        'penerimaan_id' => 'integer',
        'kategori_barang_id' => 'integer',
        'timbangan_manual_kirim' => 'float',
        'timbangan_manual_terima' => 'float',
    ];

    protected $with = ['barang'];

    public function barang()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }
}
