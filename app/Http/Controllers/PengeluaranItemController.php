<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PengeluaranItem;

class PengeluaranItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function destroy(PengeluaranItem $pengeluaranItem)
    {
        $pengeluaranItem->delete();
        return ['message' => 'ok'];
    }
}
