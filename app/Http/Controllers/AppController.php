<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class AppController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkRole')->except('checkAuth');
    }

    public function index()
    {
        return view('layouts.app');
    }

    public function checkAuth(Request $request) 
    {
        $sql = "SELECT * from menus m JOIN user_rights ur ON ur.menu_id = m.id WHERE ur.user_id = ?";
        $menus = DB::select($sql, [auth()->user()->id]);

        $rights = array_map(function ($r) {
            return $r->url;
        }, $menus);

        if (!in_array($request->route, $rights)) {
            return response('Unauthorized', 403);
        }

        return 'OK';
    }
}
