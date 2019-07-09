<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAuth extends Model
{
    protected $fillable = ['user_id', 'auth', 'label', 'description', 'allow'];

    protected $casts = [
        'user_id' => 'integer',
        'allow' => 'boolean'
    ];
}
