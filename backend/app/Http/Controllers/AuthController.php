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

    // 🔥 WAJIB hash password
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // 🔥 auto login setelah register
    Auth::login($user);

    // 🔥 langsung arahkan ke pricing
    return redirect('/#harga')
        ->with('success', 'Register berhasil! Silakan pilih paket.');
}

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();

        // 🔥 cek subscription
        $subscription = $user->subscription;

        if ($subscription 
            && $subscription->status === 'active' 
            && $subscription->expired_at > now()) {

            return redirect()->intended('/dashboard')
                ->with('success', 'Login berhasil!');
        }

        // ❌ belum subscribe
        return redirect('/#harga')
            ->with('info', 'Silakan pilih paket terlebih dahulu.');
    }

    return back()
        ->with('error', 'Email atau password salah')
        ->onlyInput('email');
}

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