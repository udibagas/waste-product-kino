<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InOutStockWp extends Model
{
    protected $fillable = [
        'tanggal', 'location_id', 'sloc', 'mvt', 'plant',
        'stock_in', 'stock_out', 'no_aju', 'no_sj',
        'material', 'material_description',
        'qty_in', 'qty_out', 'posting_date', 'mat_doc', 'mat',
        'batch', 'doc_date', 'entry_date', 'bun'
    ];

    protected $with = ['location'];

    public function location() {
        return $this->belongsTo(Location::class, 'plant', 'plant');
    }

}
