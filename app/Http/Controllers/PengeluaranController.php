<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PengeluaranRequest;
use App\Pengeluaran;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    { }

    public function show(Pengeluaran $pengeluaran)
    {
        return $pengeluaran->with(['items']);
    }

    public function store(PengeluaranRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        return Pengeluaran::create($input);
    }

    public function update(PengeluaranRequest $request, Pengeluaran $pengeluaran)
    {
        $pengeluaran->update($request->all());
        return $pengeluaran;
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        // TODO: check dulu
        $pengeluaran->delete();
        return ['message' => 'ok'];
    }
}
