<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use App\Http\Requests\LocationRequest;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'name';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return Location::when($request->keyword, function ($q) use ($request) {
            return $q->where('name', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('plant', 'LIKE', '%' . $request->keyword . '%');
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    public function store(LocationRequest $request)
    {
        return Location::create($request->all());
    }

    public function update(Location $location, LocationRequest $request)
    {
        $location->update($request->all());
        return $location;
    }

    public function destroy(Location $location)
    {
        // TODO: check dulu
        $location->delete();
        return ['message' => 'ok'];
    }

    public function getList()
    {
        return Location::all();
    }
}
