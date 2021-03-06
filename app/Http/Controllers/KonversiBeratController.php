<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KonversiBerat;
use Illuminate\Support\Facades\DB;

class KonversiBeratController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'material_id';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return KonversiBerat::when($request->keyword, function ($q) use ($request) {
            return $q->where('material_id', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('material_description', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('finished_good', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('kategori_jual', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('keterangan', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function store(Request $request)
    {
        // input batch
        if ($request->rows)
        {
            try {
                DB::beginTransaction();
                foreach ($request->rows as $row) {
                        $konversi = KonversiBerat::where('material_id', $row['material_id'])->first();

                        if ($konversi) {
                            DB::table('konversi_berats')->where('id', $konversi->id)->update($row);
                        } else {
                            DB::table('konversi_berats')->insert($row);
                        }
                    }

                DB::commit();
                return 'Data imported successfully. You can close this window or uplad more files.<br>';
            } catch (\Exception $e) {
                DB::rollBack();
                return '[ERROR] Failed to import data. ' . $e->getMessage().'<br> ' ;
            }
        }

        $request->validate([
            'kategori_jual' => 'required',
            'finished_good' => 'required',
            'material_id' => 'required',
            'material_description' => 'required',
            'berat' => 'required|numeric'
        ]);

        $konversiBerat = KonversiBerat::create($request->all());
        return $konversiBerat;
    }

    public function update(KonversiBerat $konversiBerat, Request $request)
    {
        $request->validate([
            'kategori_jual' => 'required',
            'finished_good' => 'required',
            'material_id' => 'required',
            'material_description' => 'required',
            'berat' => 'required|numeric'
        ]);

        $konversiBerat->update($request->all());
        return $konversiBerat;
    }

    public function destroy(KonversiBerat $konversiBerat)
    {
        $konversiBerat->delete();
        return ['message' => 'Data berhasil dihapus'];
    }
}
