<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockWp extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'plant', 'sloc', 'mvt', 'posting_date', 'mat_doc',
        'material', 'material_description', 'doc_date',
        'entry_date', 'time', 'bun', 'quantity', 'stock'
    ];
}
