<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PenjualanRequest;
use App\Penjualan;
use App\Events\PenjualanSubmitted;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return Penjualan::selectRaw('penjualans.*, users.name AS [user]')
            ->join('users', 'users.id', '=', 'penjualans.user_id')
            ->when($request->keyword, function ($q) use ($request) {
                return $q->where('no_sj', 'LIKE', '%' . $request->keyword . '%');
            })->when($request->pembeli_id, function ($q) use ($request) {
                return $q->whereIn('pembeli_id', $request->pembeli_id);
            })->when($request->location_id, function ($q) use ($request) {
                return $q->whereIn('penjualans.location_id', $request->location_id);
            })->when($request->status, function ($q) use ($request) {
                return $q->whereIn('status', $request->status);
            })->when($request->status_pembayaran, function ($q) use ($request) {
                return $q->whereIn('status_pembayaran', $request->status_pembayaran);
            })->when($request->user()->role == \App\User::ROLE_USER, function ($q) use ($request) {
                return $q->where('penjualans.location_id', $request->user()->location_id);
            })->when($request->jenis, function ($q) use ($request) {
                return $q->where('jenis', $request->jenis);
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function store(PenjualanRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $id = DB::table('penjualans')->insertGetId([
                    'tanggal' => $request->tanggal,
                    'no_aju' => $request->no_aju,
                    'pembeli_id' => $request->pembeli_id,
                    'no_sj' => $request->no_sj,
                    'value' => $request->value,
                    'top_date' => $request->top_date,
                    'jembatan_timbang' => floatval($request->jembatan_timbang),
                    'user_id' => $request->user()->id,
                    'status' => $request->status,
                    'jenis' => $request->jenis,
                    'location_id' => $request->location_id,
                ]);

                if ($request->jenis == 'BB')
                {
                    DB::table('penjualan_item_bbs')->insert(array_map(function($item) use ($id) {
                        return [
                            'penjualan_id' => $id,
                            'kategori_barang_id' => $item['kategori_barang_id'],
                            'timbangan_manual' => floatval($item['timbangan_manual']),
                            'jembatan_timbang' => floatval($item['jembatan_timbang']),
                            'price_per_kg' => $item['price_per_kg'],
                            'value' => $item['value'],
                            'stock_berat' => $item['stock_berat'],
                        ];
                    }, $request->items_bb));
                }

                if ($request->jenis == 'WP')
                {
                    DB::table('penjualan_item_wps')->insert(array_map(function($item) use ($id) {
                        return [
                            'penjualan_id' => $id,
                            'material_id' => $item['material_id'],
                            'material_description' => $item['material_description'],
                            'price_per_unit' => intval($item['price_per_unit']),
                            'value' => round($item['price_per_unit'] * $item['berat_dijual']),
                            'berat' => floatval($item['berat']),
                            'berat_dijual' => round($item['berat_dijual'], 4),
                            'terjual' => round($item['terjual'], 4),
                            'kategori' => $item['kategori'],
                        ];
                    }, $request->items_wp));
                }

                $penjualan = Penjualan::find($id);

                if ($request->status == Penjualan::STATUS_SUBMITTED) {
                    event(new PenjualanSubmitted($penjualan));
                }
            });
        } catch (\Exception $e) {
            return response(['message' => 'Data gagal disimpan. '. $e->getMessage()], 500);
        }
    }

    public function update(PenjualanRequest $request, Penjualan $penjualan)
    {
        try {
            DB::transaction(function () use ($request, $penjualan) {
                DB::table('penjualans')->where('id', $penjualan->id)->update([
                    'tanggal' => $request->tanggal,
                    'pembeli_id' => $request->pembeli_id,
                    'value' => $request->value,
                    'top_date' => $request->top_date,
                    'jembatan_timbang' => floatval($request->jembatan_timbang),
                    'status' => $request->status,
                ]);

                if ($request->jenis == 'BB')
                {
                    DB::table('penjualan_item_bbs')->where('penjualan_id', $penjualan->id)->delete();
                    DB::table('penjualan_item_bbs')->insert(array_map(function($item) use ($penjualan) {
                        return [
                            'penjualan_id' => $penjualan->id,
                            'kategori_barang_id' => $item['kategori_barang_id'],
                            'timbangan_manual' => floatval($item['timbangan_manual']),
                            'jembatan_timbang' => floatval($item['jembatan_timbang']),
                            'price_per_kg' => $item['price_per_kg'],
                            'value' => $item['value'],
                            'stock_berat' => $item['stock_berat'],
                        ];
                    }, $request->items_bb));
                }

                if ($request->jenis == 'WP')
                {
                    DB::table('penjualan_item_wps')->where('penjualan_id', $penjualan->id)->delete();
                    DB::table('penjualan_item_wps')->insert(array_map(function($item) use ($penjualan) {
                        return [
                            'penjualan_id' => $penjualan->id,
                            'material_id' => $item['material_id'],
                            'material_description' => $item['material_description'],
                            'price_per_unit' => intval($item['price_per_unit']),
                            'value' => round($item['price_per_unit'] * $item['berat_dijual']),
                            'berat' => floatval($item['berat']),
                            'berat_dijual' => round($item['berat_dijual'], 4),
                            'terjual' => round($item['terjual'], 4),
                            'kategori' => $item['kategori'],
                        ];
                    }, $request->items_wp));
                }

                if ($request->status == Penjualan::STATUS_SUBMITTED) {
                    event(new PenjualanSubmitted($penjualan));
                }
            });
        } catch (\Exception $e) {
            return response(['message' => 'Data gagal disimpan. '. $e->getMessage()], 500);
        }
    }

    public function destroy(Penjualan $penjualan)
    {
        if ($penjualan->status > 0) {
            return response(['message' => 'Penjualan sudah di submit.'], 500);
        }

        $penjualan->delete();
        return ['message' => 'ok'];
    }

    public function printSlip(Penjualan $penjualan)
    {
        return view('penjualan.print_slip', ['penjualan' => $penjualan]);
    }

    public function getLastRecord(Request $request)
    {
        return Penjualan::whereRaw('YEAR(tanggal) = ? ', [$request->tahun])
            ->orderBy('id', 'DESC')->first();
    }

    public function getItemWpSummary(Penjualan $penjualan)
    {
        return $penjualan->summaryItems();
    }
}
