<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $token = $user->createToken('EavSystem')->accessToken;

        return response()->json([
            'user' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
            'token' => $token
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('EavSystem')->accessToken;

            return response()->json([
                'user' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'token' => $token
            ], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'user' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'message' => 'Logged out successfully'
            ], 200);
        }

        return response()->json(['message' => 'No active session'], 400);
    }
}
