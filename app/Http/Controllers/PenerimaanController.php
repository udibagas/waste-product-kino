<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PenerimaanRequest;
use App\Penerimaan;
use App\PenerimaanItem;
use App\Events\PenerimaanSubmitted;

class PenerimaanController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return Penerimaan::when($request->keyword, function ($q) use ($request) {
            return $q->where('no_sj', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('lokasi_asal', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('lokasi_terima', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('penerima', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function show(Penerimaan $penerimaan)
    {
        return $penerimaan->with(['items']);
    }

    public function store(PenerimaanRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $penerimaan = Penerimaan::create($input);
        $penerimaan->items()->createMany($request->items);

        if ($request->status == 1) {
            event(new PenerimaanSubmitted($penerimaan));
        }

        return $penerimaan;
    }

    public function update(PenerimaanRequest $request, Penerimaan $penerimaan)
    {
        $penerimaan->update($request->all());

        foreach ($request->items as $i) {
            PenerimaanItem::find($i['id'])->update($i);
        }

        if ($request->status == 1) {
            event(new PenerimaanSubmitted($penerimaan));
        }

        return $penerimaan;
    }

    public function destroy(Penerimaan $penerimaan)
    {
        if ($penerimaan->status > 0) {
            return response(['message' => 'Penerimaan sudah di submit.'], 500);
        }

        $penerimaan->items()->delete();
        $penerimaan->delete();
        return ['message' => 'ok'];
    }
}
