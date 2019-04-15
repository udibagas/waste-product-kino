<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SkemaApprovalPenjualan;
use App\Http\Requests\SkemaApprovalPenjualanRequest;

class SkemaApprovalPenjualanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'location_id';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return SkemaApprovalPenjualan::selectRaw('skema_approval_penjualans.*')
            ->join('locations', 'locations.id', '=', 'skema_approval_penjualans.location_id')
            ->join('users', 'users.id', '=', 'skema_approval_penjualans.user_id')
            ->when($request->location_id, function($q) use($request) {
                return $q->whereIn('skema_approval_penjualans.location_id', $request->location_id);
            })->when($request->level, function ($q) use ($request) {
                return $q->whereIn('skema_approval_penjualans.level', $request->level);
            })->when($request->keyword, function ($q) use ($request) {
                return $q->where('locations.name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('locations.plant', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $request->keyword . '%');
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function store(SkemaApprovalPenjualanRequest $request)
    {
        return SkemaApprovalPenjualan::create($request->all());
    }

    public function update(SkemaApprovalPenjualan $skemaApprovalPenjualan, SkemaApprovalPenjualanRequest $request)
    {
        $skemaApprovalPenjualan->update($request->all());
        return $skemaApprovalPenjualan;
    }

    public function destroy(SkemaApprovalPenjualan $skemaApprovalPenjualan)
    {
        $skemaApprovalPenjualan->delete();
        return ['message' => 'ok'];
    }
}
