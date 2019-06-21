<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PengajuanPenjualanItemWp;

class PengajuanPenjualanItemWpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function destroy(PengajuanPenjualanItemWp $pengajuanPenjualanItemWp)
    {
        $pengajuanPenjualanItemWp->delete();
        return ['message' => 'ok'];
    }
}
