<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PembeliRequest;
use App\Pembeli;

class PembeliController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'nama';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return Pembeli::when($request->keyword, function ($q) use ($request) {
            return $q->where('nama', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('kontak', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('alamat', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('nomor_rekening', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('pemegang_rekening', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('bank', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function store(PembeliRequest $request)
    {
        return Pembeli::create($request->all());
    }

    public function update(Pembeli $pembeli, PembeliRequest $request)
    {
        $pembeli->update($request->all());
        return $pembeli;
    }

    public function destroy(Pembeli $pembeli)
    {
        // TODO: check di transaksi dulu,
        $pembeli->delete();
        return ['message' => 'ok'];
    }

    public function getList(Request $request)
    {
        return Pembeli::orderBy('nama', 'ASC')->get();
    }
}
