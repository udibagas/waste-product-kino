<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRight extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = ['user_id', 'menu_id'];
}
