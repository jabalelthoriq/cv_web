<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        if (!Auth::attempt($req->only('email','password'))) {
            return response()->json(['error'=>'Unauthorized'], 401);
        }

        return response()->json(['user'=>Auth::user()]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message'=>'Logged out']);
    }
}
