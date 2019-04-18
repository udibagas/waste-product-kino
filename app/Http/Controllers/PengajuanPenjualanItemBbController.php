<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PengajuanPenjualanItemBb;

class PengajuanPenjualanItemBbController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function destroy(PengajuanPenjualanItemBb $pengajuanPenjualanItemBb)
    {
        $pengajuanPenjualanItemBb->delete();
        return ['message' => 'ok'];
    }
}
