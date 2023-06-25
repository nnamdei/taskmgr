<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        /**
         * @var User $user 
         */

        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response([
                'status' => 'error',
                'message' => 'Invalid login credentials'
            ], 400);
        }
        $user = Auth::user();
        $token =  $user->createToken('main')->accessToken;

        return response([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token
        ], 200);
    }

    public function signup(SignupRequest $request)
    {

        $data = $request->validated();
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token =  $user->createToken('main')->accessToken;

        return response([
            'status' => 'success',
            'message' => 'User registered successfully',
            'token' => $token
        ], 201);
    }

    public function logout(Request $request)
    {
    }
}
