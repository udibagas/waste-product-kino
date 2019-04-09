<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'checkRole']);
    }

    public function index()
    {
        return view('layouts.app');
    }
}
