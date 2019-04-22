<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRight extends Model
{
    protected $fillable = ['user_id', 'menu_id'];
}
