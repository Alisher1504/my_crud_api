<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    
    public function register(Request $request) {

        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'unique:users', 'email'],
            'password' => ['required', 'string', 'confirmed']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('auth')->plainTextToken;

        $res = [
            'user' => $user,
            'token' => $token
        ];

        return [$res, 201];

    }

    public function login(Request $request) {

        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {

            return response([
                'message' => 'Login or Email error'
            ], 401);

        }

        $token = $user->createToken('auth')->plainTextToken;

        $res = [
            'user' => $user,
            'token' => $token
        ];


        return [$res, 201];

    }


    public function logout() {

        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logout successfully'
        ];

    }


}
