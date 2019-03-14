<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KonversiBerat;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KonversiBeratImport;

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
                    ->orWhere('material_description', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file')) 
        {
            $file = $request->file('file');
            $fileName = time().$file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            if (!in_array(strtolower($extension), ['xls', 'xlsx', 'csv', 'ods'])) {
                return response('File extension not supported', 500);
            }

            try {
                $file->move('files/', $fileName);
            } catch (\Exception $e) {
                return response('Failed to move file', 500);
            }

            Excel::import(new KonversiBeratImport, 'files/'.$fileName);
            unlink(public_path( 'files/' . $fileName));
            return ['message' => 'ok'];
        }

        return ['message' => 'no file uploaded'];
    }
}
