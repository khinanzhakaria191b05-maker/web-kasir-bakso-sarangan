<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt([...$cred, 'is_active' => true])) {
            $request->session()->regenerate();

            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('kasir');   // sebelumnya 'pos'
        }

        return back()->withErrors([
            'username' => 'Username/password salah atau akun nonaktif.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
