<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = ['icon', 'label', 'url', 'parent_id', 'order'];

    protected $with = ['children'];

    protected $hidden = ['created_at', 'updated_at'];

    public function children() 
    {
        if (auth()->user()->role == \App\User::ROLE_ADMIN) {
            return $this->hasMany(self::class, 'parent_id', 'id');
        }

        $rights = UserRight::where('user_id', auth()->user()->id)->get();
        $rights = array_map(function($r) {
            return $r['menu_id'];
        }, $rights->toArray());

        return $this->hasMany(self::class, 'parent_id', 'id')->whereIn('id', $rights);
    }
}
