<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PengeluaranRequest;
use App\Pengeluaran;
use App\PengeluaranItem;

class PengeluaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'tanggal';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return Pengeluaran::when($request->keyword, function ($q) use ($request) {
            return $q->where('no_sj', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('lokasi_asal', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('lokasi_terima', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('penerima', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function store(PengeluaranRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $pengeluaran = Pengeluaran::create($input);
        $pengeluaran->items()->createMany($request->items);

        if ($request->status == 1) {
            // TODO: kalau status = 1 (submit) langsung kurangi stok

        }

        return $pengeluaran->with(['items'])->get();
    }

    public function update(PengeluaranRequest $request, Pengeluaran $pengeluaran)
    {
        $pengeluaran->update($request->all());

        foreach ($request->items as $i) 
        {
            if (isset($i['id'])) {
                PengeluaranItem::find($i['id'])->update($i);
            } else {
                $pengeluaran->items()->create($i);
            }
        }

        if ($request->status == 1) {
            // TODO: kalau status = 1 (submit) langsung kurangi stok

        }

        return $pengeluaran;
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        // TODO: check dulu
        $pengeluaran->items()->delete();
        $pengeluaran->delete();
        return ['message' => 'ok'];
    }

    public function getList()
    {
        return Pengeluaran::where('status', 1)->get();
    }
}
