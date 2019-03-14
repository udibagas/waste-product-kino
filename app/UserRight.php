<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRight extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = ['user_id', 'icon', 'label', 'url', 'parent_id', 'order'];

    protected $with = ['children'];

    protected $hidden = ['created_at', 'updated_at'];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
