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
        if (auth()->user()->role == \App\User::ROLE_ADMIN) {
            return 'OK';
        }

        $sql = "SELECT * 
            FROM menus m 
            JOIN user_rights ur 
                ON ur.menu_id = m.id 
            WHERE ur.user_id = ? 
                AND m.url = ?";
        
        $allowed = DB::select($sql, [auth()->user()->id, $request->route]);

        if (!$allowed) {
            return response('Unauthorized', 403);
        }

        return 'OK';
    }
}
