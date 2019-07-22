<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InOutStockWp;

class InOutStockWpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return InOutStockWp::selectRaw('in_out_stock_wps.*')
            ->join('locations', 'locations.plant', '=', 'in_out_stock_wps.plant')
            ->when($request->range, function($q) use ($request) {
                return $q->whereBetween('tanggal', $request->range);
            })->when($request->keyword, function ($q) use ($request) {
                return $q->where('no_sj', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('no_aju', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('locations.name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('material', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('material_description', 'LIKE', '%' . $request->keyword . '%');
            })->when($request->plant, function ($q) use ($request) {
                if (is_array($request->plant)) {
                    return $q->whereIn('in_out_stock_wps.plant', $request->plant);
                } else {
                    return $q->where('in_out_stock_wps.plant', $request->plant);
                }
            })->when($request->material, function ($q) use ($request) {
                if (is_array($request->material)) {
                    return $q->whereIn('material', $request->material);
                } else {
                    return $q->where('material', $request->material);
                }
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }
}
