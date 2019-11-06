<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PengajuanPenjualanRequest;
use App\PengajuanPenjualan;
use App\Events\PengajuanPenjualanSubmitted;
use Carbon\Carbon;
use App\Events\PengajuanPenjualanApproved1;
use App\Events\PengajuanPenjualanApproved2;
use App\Events\PengajuanPenjualanRejected1;
use App\Events\PengajuanPenjualanRejected2;
use App\SkemaApprovalPenjualan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // validasi nomor aju dulu
        $no_aju = 0;
        $lastRecord = PengajuanPenjualan::whereRaw('YEAR(tanggal) = ? ', [date('Y')])->orderBy('id', 'DESC')->first();

        if ($lastRecord) {
            $no_aju = (int) substr($lastRecord->no_aju, 0, 4);
        }

        try {
            DB::transaction(function () use ($request, $no_aju) {
                $id = DB::table('pengajuan_penjualans')->insertGetId([
                    'tanggal' => $request->tanggal,
                    'no_aju' => str_pad($no_aju + 1, 4, "0", STR_PAD_LEFT).date('/m/Y').'/AJU',
                    'period_from' => $request->period ? $request->period[0] : date('Y-m-d'),
                    'period_to' => $request->period ? $request->period[1] : date('Y-m-d'),
                    'jenis' => $request->jenis,
                    'mvt_type' => is_array($request->mvt_type) ? implode(',', $request->mvt_type) : '',
                    'sloc' => $request->sloc,
                    'user_id' => $request->user()->id,
                    'location_id' => $request->location_id,
                    'status' => $request->status,
                ]);

                if ($request->jenis == 'BB') {
                    DB::table('pengajuan_penjualan_item_bbs')->insert(array_map(function($item) use ($id) {
                        return [
                            'pengajuan_penjualan_id' => $id,
                            'kategori_barang_id' => $item['kategori_barang_id'],
                            'eun' => $item['eun'],
                            'timbangan_manual' => $item['timbangan_manual'],
                            'stock_berat' => $item['stock_berat'],
                        ];
                    }, $request->items_bb));
                }

                if ($request->jenis == 'WP') {
                    DB::table('pengajuan_penjualan_item_wps')->insert(array_map(function($item) use ($id) {
                        return [
                            'pengajuan_penjualan_id' => $id,
                            'kategori' => $item['kategori'],
                            'material_id' => $item['material'],
                            'material_description' => $item['material_description'],
                            'divisi' => '-',
                            'unit' => '-',
                            'qty_reject' => 0,
                            'price_per_unit' => $item['price_per_unit'],
                            'value' => (int) ((float) $item['diajukan'] * (int) $item['price_per_unit']),
                            'berat' => (float) $item['diajukan'],
                            'terjual' => 0,
                            'stock' => (float) $item['stock'],
                        ];
                    }, $request->items_wp));
                }

                $pengajuanPenjualan = PengajuanPenjualan::find($id);

                if ($request->status == PengajuanPenjualan::STATUS_SUBMITTED) {
                    event(new PengajuanPenjualanSubmitted($pengajuanPenjualan));
                }

                return $pengajuanPenjualan;
            });
        } catch (\Exception $e) {
            return response(['message' => 'Data gagal disimpan.'. $e->getMessage()], 500);
        }
    }

    public function update(PengajuanPenjualanRequest $request, PengajuanPenjualan $pengajuanPenjualan)
    {
        try {
            DB::transaction(function () use ($request, $pengajuanPenjualan) {
                DB::table('pengajuan_penjualans')->where('id', $pengajuanPenjualan->id)->update([
                    'tanggal' => $request->tanggal,
                    'period_from' => $request->period ? $request->period[0] : date('Y-m-d'),
                    'period_to' => $request->period ? $request->period[1] : date('Y-m-d'),
                    'status' => $request->status,
                ]);

                if ($request->jenis == 'BB')
                {
                    DB::table('pengajuan_penjualan_item_bbs')
                        ->where('pengajuan_penjualan_id', $pengajuanPenjualan->id)
                        ->delete();

                    DB::table('pengajuan_penjualan_item_bbs')->insert(array_map(function($item) use ($pengajuanPenjualan) {
                        return [
                            'pengajuan_penjualan_id' => $pengajuanPenjualan->id,
                            'kategori_barang_id' => $item['kategori_barang_id'],
                            'eun' => $item['eun'],
                            'timbangan_manual' => $item['timbangan_manual'],
                            'stock_berat' => floatval($item['stock_berat']),
                        ];
                    }, $request->items_bb));
                }

                if ($request->jenis == 'WP')
                {
                    DB::table('pengajuan_penjualan_item_wps')
                        ->where('pengajuan_penjualan_id', $pengajuanPenjualan->id)
                        ->delete();

                    DB::table('pengajuan_penjualan_item_wps')->insert(array_map(function($item) use ($pengajuanPenjualan) {
                        return [
                            'pengajuan_penjualan_id' => $pengajuanPenjualan->id,
                            'kategori' => $item['kategori'],
                            'material_id' => $item['material'],
                            'material_description' => $item['material_description'],
                            'divisi' => '-',
                            'unit' => '-',
                            'qty_reject' => 0,
                            'price_per_unit' => $item['price_per_unit'],
                            'value' => (int) ((float) $item['diajukan'] * (int) $item['price_per_unit']),
                            'berat' => (float) $item['diajukan'],
                            'terjual' => 0,
                            'stock' => (float) $item['stock'],
                        ];
                    }, $request->items_wp));
                }

                if ($request->status == PengajuanPenjualan::STATUS_SUBMITTED) {
                    event(new PengajuanPenjualanSubmitted($pengajuanPenjualan));
                }

                return $pengajuanPenjualan;
            });
        } catch (\Exception $e) {
            return response(['message' => 'Data gagal disimpan.'. $e->getMessage()], 500);
        }
    }

    public function destroy(PengajuanPenjualan $pengajuanPenjualan)
    {
        // status bukan draft
        if ($pengajuanPenjualan->status > PengajuanPenjualan::STATUS_DRAFT) {
            return response(['message' => 'Pengajuan penjualan sudah di submit.'], 500);
        }

        $pengajuanPenjualan->delete();
        return ['message' => 'ok'];
    }

    public function getList(Request $request)
    {
        return PengajuanPenjualan::where('jenis', $request->jenis)
            ->where(function($q) {
                return $q->where('status', PengajuanPenjualan::STATUS_APPROVED)
                    ->orWhere('status', PengajuanPenjualan::STATUS_PARTIALLY_PROCESSED);
            })->get();
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

    public function getLastRecord(Request $request)
    {
        return PengajuanPenjualan::whereRaw('YEAR(tanggal) = ? ', [$request->tahun])
            ->orderBy('id', 'DESC')->first();
    }

    public function getItemWpSummary(PengajuanPenjualan $pengajuanPenjualan)
    {
        return $pengajuanPenjualan->summaryItems;
    }
}
