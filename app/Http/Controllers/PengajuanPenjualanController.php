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
use App\Events\PengajuanPenjualanRejected1;
use App\Events\PengajuanPenjualanRejected2;
use App\SkemaApprovalPenjualan;
use Illuminate\Support\Facades\Auth;

class PengajuanPenjualanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['approvalForm']);
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return PengajuanPenjualan::when($request->keyword, function ($q) use ($request) {
            return $q->where('no_aju', 'LIKE', '%' . $request->keyword . '%');
        })->when($request->jenis, function ($q) use ($request) {
            return $q->where('jenis', $request->jenis);
        })->when($request->location_id, function ($q) use ($request) {
            return $q->whereIn('location_id', $request->location_id);
        })->when($request->approval1_status, function ($q) use ($request) {
            return $q->whereIn('approval1_status', $request->approval1_status);
        })->when($request->approval2_status, function ($q) use ($request) {
            return $q->whereIn('approval2_status', $request->approval2_status);
        })->when($request->status, function ($q) use ($request) {
            return $q->whereIn('status', $request->status);
        })->when($request->user()->role == \App\User::ROLE_USER, function ($q) use ($request) {
            return $q->where('location_id', $request->user()->location_id);
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

        if ($request->status == PengajuanPenjualan::STATUS_SUBMITTED) {
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

        if ($request->status == PengajuanPenjualan::STATUS_SUBMITTED) {
            event(new PengajuanPenjualanSubmitted($pengajuanPenjualan));
        }

        return $pengajuanPenjualan;
    }

    public function destroy(PengajuanPenjualan $pengajuanPenjualan)
    {
        // status bukan draft
        if ($pengajuanPenjualan->status > PengajuanPenjualan::STATUS_DRAFT) {
            return response(['message' => 'Pengajuan penjualan sudah di submit.'], 500);
        }

        $pengajuanPenjualan->itemsBb()->delete();
        $pengajuanPenjualan->itemsWp()->delete();
        $pengajuanPenjualan->delete();
        return ['message' => 'ok'];
    }

    public function getList(Request $request)
    {
        return PengajuanPenjualan::where('jenis', $request->jenis)
            ->where('status', PengajuanPenjualan::STATUS_APPROVED)->get();
    }

    /**
     * Fungsi untuk menampilkan form approval pengajuan penjualan
     */
    public function approvalForm(PengajuanPenjualan $pengajuanPenjualan, Request $request)
    {
        // cek bener gak approvernya
        $skema = SkemaApprovalPenjualan::where('location_id', $pengajuanPenjualan->location_id)
            ->where('level', $request->level)
            ->first();
        
        if ($skema->user->api_token != $request->api_token) {
            return response('Anda tidak berhak melakukan approval untuk request ini.', 403);
        }

        Auth::login($skema->user);

        return view('pengajuanPenjualan.approvalForm', [
            'data' => $pengajuanPenjualan
        ]);
    }

    /**
     * Proses approval pengajuan, bisa reject, bisa berkali2
     */
    public function approve(PengajuanPenjualan $pengajuanPenjualan, Request $request)
    {
        $skema = SkemaApprovalPenjualan::where('location_id', $pengajuanPenjualan->location_id)
            ->where('level', $request->level)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$skema) {
            return response(['message' => 'Anda tidak berhak melakukan approval untuk request ini.'], 500);
        }

        if ($pengajuanPenjualan->status == PengajuanPenjualan::STATUS_DRAFT) {
            return response(['message' => 'Pengajuan belum disubmit'], 500);
        }

        if ($request->level == 1) 
        {
            $data = [
                'approval1_status' => $request->status,
                'approval1_by' => $request->user()->id,
                'approval1_time' => Carbon::now()
            ];
        }

        if ($request->level == 2) 
        {
            if ($pengajuanPenjualan->approval1_status != PengajuanPenjualan::STATUS_APPROVAL_APPROVED) {
                return response(['message' => 'Pengajuan level 1 belum/tidak disetujui'], 500);
            }

            $data = [
                'approval2_status' => $request->status,
                'approval2_by' => $request->user()->id,
                'approval2_time' => Carbon::now()
            ];
        }

        if ($request->status == PengajuanPenjualan::STATUS_APPROVAL_REJECTED) {
            $data['status'] = PengajuanPenjualan::STATUS_REJECTED;
        }

        $pengajuanPenjualan->update($data);

        if ($request->level == 1) 
        {
            if ($request->status == PengajuanPenjualan::STATUS_APPROVAL_APPROVED) {
                event(new PengajuanPenjualanApproved1($pengajuanPenjualan));
            }

            if ($request->status == PengajuanPenjualan::STATUS_APPROVAL_REJECTED) {
                event(new PengajuanPenjualanRejected1($pengajuanPenjualan));
            }
        }

        if ($request->level == 2) 
        {
            if ($request->status == PengajuanPenjualan::STATUS_APPROVAL_APPROVED) {
                $pengajuanPenjualan->status = PengajuanPenjualan::STATUS_APPROVED;
                $pengajuanPenjualan->save();
                event(new PengajuanPenjualanApproved2($pengajuanPenjualan));
            }

            if ($request->status == PengajuanPenjualan::STATUS_APPROVAL_REJECTED) {
                $pengajuanPenjualan->status = PengajuanPenjualan::STATUS_REJECTED;
                $pengajuanPenjualan->save();
                event(new PengajuanPenjualanRejected2($pengajuanPenjualan));
            }
        }

        $pengajuanPenjualan->approvals()->create([
            'user_id' => $request->user()->id,
            'note' => $request->note,
            'status' => $request->status,
            'level' => $request->level
        ]);

        return $pengajuanPenjualan;
    }

    public function getApprovalHistory(PengajuanPenjualan $pengajuanPenjualan)
    {
        return $pengajuanPenjualan->approvals;
    }
}
