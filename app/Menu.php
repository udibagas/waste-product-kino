<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = ['icon', 'label', 'url', 'parent_id', 'order'];

    protected $with = ['children'];

    protected $hidden = ['created_at', 'updated_at'];

    public function children() {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
