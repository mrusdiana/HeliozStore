<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {

        $user = User::create($request->validated());

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('auth')->plainTextToken,
        ], 201);
    }

    public function login(LoginRequest $request) {
         $user = User::where('email', $request->email)->first();

         if(! $user || !Hash::check($request->password, $user->password)) {
            
            throw ValidationException::withMessages([
                'email' => ["Incorrect email or password"],
            ]);
         }

         return response()->json([
            'user'=> $user,
            'token'=> $user->createToken('auth')->plainTextToken,
         ]);
    }

    public function me (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function logout (Request $request) {

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => "Sign Out"]);
    }
}

