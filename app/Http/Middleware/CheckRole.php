<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->role == \App\User::ROLE_USER) 
        {
            $sql = "SELECT * from menus m JOIN user_rights ur ON ur.menu_id = m.id WHERE ur.user_id = ?";
            $menus = DB::select($sql, [auth()->user()->id]);

            $rights = array_map(function ($r) {
                return 'app' . $r->url;
            }, $menus);

            if (!in_array(request()->path(), $rights)) {
                return redirect('/app/home');
            }
        }

        return $next($request);
    }
}
