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
        $request->validate([
            'npr' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'npr' => $request->npr,
            'password' => $request->password
        ])) {

            $request->session()->regenerate();

            $role = auth()->user()->idRole;

            if ($role == 1) return redirect('/admin/dashboard');
            if ($role == 2) return redirect('/dosen/dashboard');
            if ($role == 3) return redirect('/mahasiswa/dashboard');

            return abort(403);
        }

        return back()->withErrors([
            'npr' => 'NPR atau password salah'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
