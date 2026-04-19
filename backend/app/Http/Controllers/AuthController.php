<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ✅ REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Redirect ke login dengan session success
        return redirect()->route('login')->with('success', 'Register berhasil! Silakan login.');
    }

    // ✅ LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Gunakan Auth facade bawaan Laravel
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            // Regenerate session untuk security
            $request->session()->regenerate();
            
            // Redirect ke dashboard
            return redirect()->route('dashboard')->with('success', 'Login berhasil! Selamat datang kembali.');
        }

        return back()->with('error', 'Email atau password salah')->onlyInput('email');
    }

    // ✅ LOGOUT (Web)
    public function logoutWeb(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }

    // ✅ GET USER (API)
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // ✅ LOGOUT (API)
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}