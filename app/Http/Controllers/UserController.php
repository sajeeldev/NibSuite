<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'users' => $users,
            'employee_ids' => $users->pluck('employee_id'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $prefix = 'nibsuite-' . date('Y') . '-';

        // Generate employee_id 
        $validated['employee_id'] = IdGenerator::generate([
            'table'  => 'users',
            'field'  => 'employee_id',
            'length' => strlen($prefix) + 6,
            'prefix' => $prefix,
        ]);

        // create the user 
        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'user'    => $user,
            'employee_id' => $user->employee_id,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        $employee_id = $user->employee_id;

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user,
            'employee_id' => $employee_id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::find($id)->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
