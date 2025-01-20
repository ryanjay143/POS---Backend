<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Email is already taken',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'access_token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'credential' => 'required|string', // This can be email or username
            'password' => 'required|string',
        ]);

        $credential = $data['credential'];

        // Check if the provided credential is an email or username
        $fieldType = filter_var($credential, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // Attempt to authenticate with the determined field type
        if (!auth()->attempt([$fieldType => $credential, 'password' => $data['password']])) {
            return response()->json([
                'message' => 'Invalid credentials, Please try again!',
            ], 401);
        }

        // Generate a token for the authenticated user
        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => auth()->user(),
            'access_token' => $token,
        ]);
    }



    
    
}
