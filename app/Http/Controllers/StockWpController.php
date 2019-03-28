<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockWp;
use Illuminate\Support\Facades\DB;

class StockWpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'plant';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return StockWp::when($request->keyword, function ($q) use ($request) {
            return $q->where('material', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('material_description', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('plant', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('sloc', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('mvt', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('mat_doc', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function getStock(Request $request)
    {

    }

    public function store(Request $request)
    {
        try 
        {
            DB::beginTransaction();
            foreach ($request->rows as $i => $row) 
            {
                $stock = StockWp::where('plant', $row['plant'])
                    ->where('material', $row['material'])
                    ->first();

                if ($stock) {
                    $row['quantity'] += $stock->quantity;
                    DB::table('stock_wps')->where('id', $stock->id)->update($row);
                } else {
                    $row['quantity'] = $row['quantity'] == null ? 0 : $row['quantity'];
                    DB::table('stock_wps')->insert($row);
                }
            }

            DB::commit();
            return 'Data imported successfully. You can close this window or uplad more file.<br>';
        } catch (\Exception $e) {
            DB::rollBack();
            return '[ERROR] Failed to import data. ' . $e->getMessage().'<br>';
        }
    }
}
