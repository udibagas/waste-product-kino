<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PenerimaanRequest;
use App\Penerimaan;

class PenerimaanController extends Controller
{
    public function index(Request $request)
    {

    }

    public function show(Penerimaan $penerimaan)
    {
        return $penerimaan->with(['items']);
    }

    public function store(PenerimaanRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        return Penerimaan::create($input);
    }

    public function update(PenerimaanRequest $request, Penerimaan $penerimaan)
    {
        $penerimaan->update($request->all());
        return $penerimaan;
    }

    public function destroy(Penerimaan $penerimaan)
    {
        // TODO: check dulu
        $penerimaan->delete();
        return ['message' => 'ok'];
    }
}
