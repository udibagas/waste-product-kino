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
        $sort = $request->sort ? $request->sort : 'plant';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return SkemaApprovalPenjualan::when($request->keyword, function ($q) use ($request) {
            return $q->where('plant', 'LIKE', '%' . $request->keyword . '%');
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
