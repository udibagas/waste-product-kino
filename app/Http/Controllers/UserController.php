<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'name';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return User::when($request->keyword, function ($q) use ($request) {
            return $q->where('users.name', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('users.email', 'LIKE', '%' . $request->keyword . '%');
        })->when($request->role, function ($q) use ($request) {
            return $q->whereIn('users.role', $request->role);
        })->when($request->status, function ($q) use ($request) {
            return $q->whereIn('users.status', $request->status);
        })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:0,1',
            'status' => 'required|boolean',
            'location_id' => 'required'
        ], [], [
            'name' => 'Name',
            'email' => 'Email',
            'role' => 'Role',
            'status' => 'Status',
            'location_id' => 'Lokasi'
        ]);

        $input = $request->all();
        $input['password'] = bcrypt($request->password);
        $user = User::create($input);
        $user->rights()->createMany($request->rights);
        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:0,1',
            'status' => 'required|boolean',
            'location_id' => 'required'
        ];

        $input = $request->all();

        if ($request->password) {
            $rules['password'] = 'required|string|min:6|confirmed';
            $input['password'] = bcrypt($request->password);
        }

        $request->validate($rules, [], [
            'name' => 'Name',
            'email' => 'Email',
            'role' => 'Role',
            'status' => 'Status',
            'location_id' => 'Lokasi'
        ]);

        $user->update($input);
        $user->rights()->delete();
        $user->rights()->createMany($request->rights);
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->user()->id) {
            return response(['message' => 'Tidak boleh menghapus user sendiri'], 500);
        }
        
        $user->delete();
        return ['message' => 'OK'];
    }

    public function getList() {
        return User::orderBy('name', 'ASC')->get();
    }
}
