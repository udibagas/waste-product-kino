<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InOutStockBb;

class InOutStockBbController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return InOutStockBb::when($request->keyword, function ($q) use ($request) {
            return $q->where('no_sj', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('plant', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('lokasi', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }
}
