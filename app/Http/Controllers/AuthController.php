<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Email is already taken',
        ]);

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'credential' => 'required|string', 
            'password' => 'required|string',
        ]);

        $credential = $data['credential'];

        // Check if the provided credential is an email or username
        $fieldType = filter_var($credential, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt to authenticate with the determined field type
        if (!auth()->attempt([$fieldType => $credential, 'password' => $data['password']])) {
            return response()->json([
                'message' => 'Invalid credentials, Please try again!',
            ], 401);
        }

        // Invalidate all existing tokens for the authenticated user
        auth()->user()->tokens()->where('name', 'auth_token')->delete();

        // Generate a new token for the authenticated user
        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => auth()->user(),
            'access_token' => $token,
        ]);
    }




    
    
}
