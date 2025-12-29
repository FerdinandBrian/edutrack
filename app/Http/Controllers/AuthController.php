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
            'nrp' => 'required_if:role,mahasiswa',
            'nip' => 'required_if:role,dosen',
            'password' => 'required|confirmed|min:6',
            'email' => 'nullable|email|unique:users,email',
            'jenis_kelamin' => 'required_if:role,mahasiswa,dosen,admin|string|max:20',
            'tanggal_lahir' => 'required_if:role,mahasiswa',
            'alamat' => 'required_if:role,mahasiswa',
            'no_telepon' => 'required_if:role,mahasiswa,dosen',
        ]);

        DB::transaction(function() use ($request) {
            // Ambil id_role dari tabel role
            $roleRecord = DB::table('role')->where('nama_role', $request->role)->first();

            if (!$roleRecord) {
                throw new \Exception("Role tidak ditemukan di database");
            }

            // 1️⃣ Simpan ke users
            $user = User::create([
                'nrp' => match($request->role) {
                    'mahasiswa' => $request->nrp,
                    'dosen' => $request->nip,
                    'admin' => $request->kode_admin,
                    default => null,
                },
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'id_role' => $roleRecord->id,
            ]);

            // 2️⃣ Simpan ke tabel role-specific
            if ($request->role === 'mahasiswa') {
                Mahasiswa::create([
                    'user_id'       => $user->id,
                    'nrp'           => $request->nrp,
                    'nama'          => $request->nama,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'alamat'        => $request->alamat,
                    'no_telepon'    => $request->no_telepon,
                    'email'         => $request->email,
                    'jurusan'       => $request->jurusan ?? null,
                    'DosenWali'     => null,
                    'PoinPortopolio'=> 0,
                    'id_role'       => $roleRecord->id,
                ]);
            } elseif ($request->role === 'dosen') {
                Dosen::create([
                    'user_id'       => $user->id,
                    'nip'           => $request->nip,
                    'nama'          => $request->nama,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'email'         => $request->email,
                    'no_telepon'    => $request->no_telepon,
                    'id_role'       => $roleRecord->id,
                ]);
            } elseif ($request->role === 'admin') {
                Admin::create([
                    'user_id' => $user->id,
                    'kode_admin' => $request->kode_admin ?? null,
                    'nama'    => $request->nama,
                    'email'   => $request->email,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'no_telepon'    => $request->no_telepon,
                    'password'=> Hash::make($request->password),
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'id_role' => $roleRecord->id,
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

        $col = \Illuminate\Support\Facades\Schema::hasColumn((new User)->getTable(), 'nrp') ? 'nrp' : 'npr';
        $user = User::where('email', $request->nrp) // bisa juga pake nrp/nip/email sesuai kebutuhan
                    ->orWhere($col, $request->nrp)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'nrp' => 'NRP / NIP / Email atau password salah',
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
            'admin'     => redirect('/dashboard/admin'),
            'dosen'     => redirect('/dashboard/dosen'),
            'mahasiswa' => redirect('/dashboard/mahasiswa'),
            default     => redirect('/login'),
        };
    }
}