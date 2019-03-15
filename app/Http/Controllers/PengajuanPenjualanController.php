<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PengajuanPenjualanRequest;
use App\PengajuanPenjualan;
use App\Events\PengajuanPenjualanSubmitted;
use App\PengajuanPenjualanItemBb;
use App\PengajuanPenjualanItemWp;
use Carbon\Carbon;
use App\Events\PengajuanPenjualanApproved1;
use App\Events\PengajuanPenjualanApproved2;

class PengajuanPenjualanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return PengajuanPenjualan::when($request->keyword, function ($q) use ($request) {
            return $q->where('no_aju', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('plant', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function show(PengajuanPenjualan $pengajuanPenjualan)
    {
        return $pengajuanPenjualan;
    }

    public function store(PengajuanPenjualanRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $pengajuanPenjualan = PengajuanPenjualan::create($input);
        
        if ($request->jenis == 'BB') {
            $pengajuanPenjualan->itemsBb()->createMany($request->items_bb);
        }

        if ($request->jenis == 'WP') {
            $pengajuanPenjualan->itemsWp()->createMany($request->items_wp);
        }

        if ($request->status == 1) {
            event(new PengajuanPenjualanSubmitted($pengajuanPenjualan));
        }

        return $pengajuanPenjualan;
    }

    public function update(PengajuanPenjualanRequest $request, PengajuanPenjualan $pengajuanPenjualan)
    {
        $pengajuanPenjualan->update($request->all());

        if ($pengajuanPenjualan->jenis == 'BB') {
            foreach ($request->items_bb as $i) {
                if (isset($i['id'])) {
                    PengajuanPenjualanItemBb::find($i['id'])->update($i);
                } else {
                    $pengajuanPenjualan->itemsBb()->create($i);
                }
            }
        }

        if ($pengajuanPenjualan->jenis == 'WP') {
            foreach ($request->items_wp as $i) {
                if (isset($i['id'])) {
                    PengajuanPenjualanItemWp::find($i['id'])->update($i);
                } else {
                    $pengajuanPenjualan->itemsWp()->create($i);
                }
            }
        }

        if ($request->status == 1) {
            event(new PengajuanPenjualanSubmitted($pengajuanPenjualan));
        }

        return $pengajuanPenjualan;
    }

    public function destroy(PengajuanPenjualan $pengajuanPenjualan)
    {
        if ($pengajuanPenjualan->status > 0) {
            return response(['message' => 'Pengajuan penjualan sudah di submit.'], 500);
        }

        $pengajuanPenjualan->itemsBb()->delete();
        $pengajuanPenjualan->itemsWp()->delete();
        $pengajuanPenjualan->delete();
        return ['message' => 'ok'];
    }

    public function getList()
    {
        return PengajuanPenjualan::where('status', 1)->get();
    }

    public function approve(Request $request, PengajuanPenjualan $pengajuanPenjualan)
    {
        if ($pengajuanPenjualan->status == 0) {
            return response(['message' => 'Pengajuan belum disubmit'], 500);
        }

        if ($request->level == 1) 
        {
            if ($pengajuanPenjualan->approval1_status > 0) {
                return response(['message' => 'Pengajuan sudah diapprove sebelumnya'], 500);
            }

            $data = [
                'approval1_status' => $request->status,
                'approval1_by' => $request->user()->id,
                'approval1_time' => Carbon::now()
            ];
        }

        if ($request->level == 2) 
        {
            if ($pengajuanPenjualan->approval1_status != 1) {
                return response(['message' => 'Pengajuan level 1 belum/tidak disetujui'], 500);
            }

            if ($pengajuanPenjualan->approval2_status > 0) {
                return response(['message' => 'Pengajuan sudah diapprove sebelumnya'], 500);
            }

            $data = [
                'approval2_status' => $request->status,
                'approval2_by' => $request->user()->id,
                'approval2_time' => Carbon::now()
            ];
        }
        
        $pengajuanPenjualan->update($data);
        
        if ($request->level == 1) {
            event(new PengajuanPenjualanApproved1($pengajuanPenjualan));
        }

        if ($request->level == 2) {
            $pengajuanPenjualan->status = 2;
            $pengajuanPenjualan->save();
            event(new PengajuanPenjualanApproved2($pengajuanPenjualan));
        }

        return $pengajuanPenjualan;
    }
}
