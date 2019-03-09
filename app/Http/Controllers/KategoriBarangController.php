<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\KategoriBarangRequest;
use App\KategoriBarang;

class KategoriBarangController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'nama';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return KategoriBarang::when($request->keyword, function ($q) use ($request) {
            return $q->where('nama', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('kode', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('jenis', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('unit', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function store(KategoriBarangRequest $request)
    {
        return KategoriBarang::create($request->all());
    }

    public function update(KategoriBarang $kategoriBarang, KategoriBarangRequest $request)
    {
        $input = $request->all();

        if ($request->harga != $kategoriBarang->harga) {
            $input['status'] = 1;
        }

        $kategoriBarang->update($input);
        return $kategoriBarang;
    }

    public function destroy(KategoriBarang $kategoriBarang)
    {
        // TODO: check di transaksi dulu,
        $kategoriBarang->delete();
        return ['message' => 'ok'];
    }

    public function getList(Request $request)
    {
        return KategoriBarang::when($request->status, function($q) use ($request) {
            return $q->where('status', $request->status);
        })->orderBy('jenis','ASC')->orderBy('nama', 'ASC')->get();
    }
}
