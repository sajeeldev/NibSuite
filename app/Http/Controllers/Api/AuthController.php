<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\RegisterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {

        $validated = $request->validated();

        try {
            // Create the user (no need for separate exists check - RegisterRequest already validates uniqueness)
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            if (! $user) {
                throw new \Exception('User creation failed');
            }

            // Generate token for the newly created user
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message'      => 'Registration successful',
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'data' => $user,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle the incoming request for login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        // Attempt to authenticate the user
        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
        $user  = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
        ]);
    }
}
