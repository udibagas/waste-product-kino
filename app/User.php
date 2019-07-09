<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class User extends Authenticatable
{
    use Notifiable;

    const ROLE_USER = 0;

    const ROLE_ADMIN = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'role',
        'department', 'location_id', 'no_karyawan', 'api_token',
        'allow_approve_kategori'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'location_id' => 'integer',
        'role' => 'integer',
        'status' => 'integer',
        'allow_approve_kategori' => 'boolean'
    ];

    protected $with = ['location', 'rights'];

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function rights() {
        return $this->hasMany(UserRight::class);
    }
}
