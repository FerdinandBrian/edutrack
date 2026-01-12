<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Admin;

class AuthController extends Controller
{
    /* ======================
        VIEW
    ======================= */
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /* ======================
        REGISTER
    ======================= */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'role' => 'required|in:mahasiswa,dosen,admin',
            'nrp' => 'required|string|max:50',
            'password' => 'required|min:6',
            'email' => 'nullable|email|unique:users,email',
            'jenis_kelamin' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string',
        ]);

        // Provide default values for required DB fields if missing
        $tanggal_lahir = $request->tanggal_lahir ?? '2000-01-01';
        $jenis_kelamin = $request->jenis_kelamin ?? 'Laki-laki';
        $no_telepon = $request->no_telepon ?? '-';

        DB::transaction(function() use ($request, $tanggal_lahir, $jenis_kelamin, $no_telepon) {
            // 1️⃣ Simpan ke users
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // 2️⃣ Simpan ke tabel role-specific
            if ($request->role === 'mahasiswa') {
                Mahasiswa::create([
                    'nrp'           => $request->nrp,
                    'user_id'       => $user->id,
                    'nama'          => $request->nama,
                    'jenis_kelamin' => $jenis_kelamin,
                    'tanggal_lahir' => $tanggal_lahir,
                    'alamat'        => $request->alamat,
                    'no_telepon'    => $no_telepon,
                    'email'         => $request->email,
                    'jurusan'       => $request->jurusan ?? null,
                    'password'      => Hash::make($request->password),
                ]);
            } elseif ($request->role === 'dosen') {
                Dosen::create([
                    'nip'           => $request->nrp,
                    'user_id'       => $user->id,
                    'nama'          => $request->nama,
                    'jenis_kelamin' => $jenis_kelamin,
                    'tanggal_lahir' => $tanggal_lahir,
                    'email'         => $request->email,
                    'no_telepon'    => $no_telepon,
                    'password'      => Hash::make($request->password),
                ]);
            } elseif ($request->role === 'admin') {
                Admin::create([
                    'kode_admin' => $request->nrp,
                    'user_id' => $user->id,
                    'nama'    => $request->nama,
                    'email'   => $request->email,
                    'tanggal_lahir' => $tanggal_lahir,
                    'no_telepon'    => $no_telepon,
                    'password'=> Hash::make($request->password),
                    'jenis_kelamin' => $jenis_kelamin,
                ]);
            }
        });

        // Redirect ke halaman login setelah register
        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    /* ======================
        LOGIN
    ======================= */
    public function login(Request $request)
    {
        $request->validate([
            'nrp'      => 'required',
            'password' => 'required',
        ]);

        $loginField = $request->nrp; // input bisa email/nip/nrp
        
        // 1. Coba cari langsung di email users
        $user = User::where('email', $loginField)->first();

        // 2. Jika tidak ketemu, cari di tabel detail (mahasiswa/dosen/admin)
        if (!$user) {
            $mhs = Mahasiswa::where('nrp', $loginField)->first();
            if ($mhs) $user = User::find($mhs->user_id);
            
            if (!$user) {
                $dsn = Dosen::where('nip', $loginField)->first();
                if ($dsn) $user = User::find($dsn->user_id);
            }
            
            if (!$user) {
                $adm = Admin::where('kode_admin', $loginField)->first();
                if ($adm) $user = User::find($adm->user_id);
            }
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'nrp' => 'Email / ID atau password salah',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectDashboard($user);
    }

    /* ======================
        LOGOUT
    ======================= */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /* ======================
        REDIRECT DASHBOARD
    ======================= */
    private function redirectDashboard(User $user)
    {
        return match ($user->role) {
            'admin'     => redirect('/admin/dashboard'),
            'dosen'     => redirect('/dosen/dashboard'),
            'mahasiswa' => redirect('/mahasiswa/dashboard'),
            default => redirect('/login'),
        };
    }
}