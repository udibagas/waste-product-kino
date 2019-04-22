<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PenerimaanItem extends Model
{
    protected $fillable = [
        'penerimaan_id', 'kategori_barang_id', 'qty_kirim', 'qty_terima',
        'eun', 'timbangan_manual_kirim', 'timbangan_manual_terima', 'keterangan'
    ];

    protected $with = ['barang'];

    public function barang()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'id');
    }
}
