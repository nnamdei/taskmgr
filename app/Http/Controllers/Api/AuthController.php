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
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response([
                'status' => 'error',
                'message' => 'Invalid login credentials'
            ], 422);
        }
        $user = Auth::user();
        $token =  $user->createToken('main')->accessToken;
        $message = 'Login successful';
        return response(compact('message', 'user', 'token'));
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
        $message = 'Signup successful';
        return response(compact('message','user', 'token'));
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
