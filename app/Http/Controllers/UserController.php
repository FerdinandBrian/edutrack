<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Dosen;
use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return redirect('/admin/admin-data');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'role' => 'required|in:admin,dosen,mahasiswa',
            'identifier' => 'required', // NRP/NIP/Kode Admin
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'jenis_kelamin' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'no_telepon' => 'required|string',
            'alamat' => 'nullable|string',
            'alamat' => 'nullable|string',
            'jurusan' => 'required_if:role,mahasiswa,dosen|nullable|string',
            'fakultas' => 'required_if:role,dosen|nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Create User (untuk login)
                $user = User::create([
                    'nama' => $request->nama,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                ]);

                // 2. Create record detail di tabel role masing-masing
                if ($request->role === 'admin') {
                    Admin::create([
                        'kode_admin' => $request->identifier,
                        'user_id' => $user->id,
                        'nama' => $request->nama,
                        'email' => $request->email,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'no_telepon' => $request->no_telepon,
                        'admin_level' => $request->admin_level ?? 'second', // Default to second admin
                        'alamat' => $request->alamat,
                    ]);
                } elseif ($request->role === 'dosen') {
                    Dosen::create([
                        'nip' => $request->identifier,
                        'user_id' => $user->id,
                        'nama' => $request->nama,
                        'email' => $request->email,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'no_telepon' => $request->no_telepon,
                        'fakultas' => $request->fakultas,
                        'no_telepon' => $request->no_telepon,
                        'fakultas' => $request->fakultas,
                        'jurusan' => $request->jurusan,
                        'alamat' => $request->alamat,
                    ]);
                } elseif ($request->role === 'mahasiswa') {
                    Mahasiswa::create([
                        'nrp' => $request->identifier,
                        'user_id' => $user->id,
                        'nama' => $request->nama,
                        'email' => $request->email,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'no_telepon' => $request->no_telepon,
                        'jurusan' => $request->jurusan,
                        'alamat' => $request->alamat,
                    ]);
                }
            });

            $redirectPath = match($request->role) {
                'admin' => '/admin/admin-data',
                'dosen' => '/admin/dosen',
                'mahasiswa' => '/admin/mahasiswa',
                default => '/admin/dashboard',
            };

            return redirect($redirectPath)->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Check if current admin is second admin trying to edit super admin
        $currentAdmin = \App\Models\Admin::where('user_id', auth()->id())->first();
        if ($currentAdmin && $currentAdmin->admin_level === 'second' && $user->role === 'admin') {
            $targetAdmin = \App\Models\Admin::where('user_id', $user->id)->first();
            $redirectPath = match($user->role) {
                'admin' => '/admin/admin-data',
                'dosen' => '/admin/dosen',
                'mahasiswa' => '/admin/mahasiswa',
                default => '/admin/dashboard',
            };
            if ($targetAdmin && $targetAdmin->admin_level === 'super') {
                return redirect($redirectPath)->withErrors(['msg' => 'Anda tidak memiliki akses untuk mengedit Super Admin.']);
            }
        }

        $detail = null;
        if ($user->role === 'admin') {
            $detail = Admin::where('user_id', $user->id)->first();
        } elseif ($user->role === 'dosen') {
            $detail = Dosen::where('user_id', $user->id)->first();
        } elseif ($user->role === 'mahasiswa') {
            $detail = Mahasiswa::where('user_id', $user->id)->first();
        }

        return view('admin.users.edit', compact('user', 'detail'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Check if current admin is second admin trying to update super admin
        $currentAdmin = \App\Models\Admin::where('user_id', auth()->id())->first();
        if ($currentAdmin && $currentAdmin->admin_level === 'second' && $user->role === 'admin') {
            $targetAdmin = \App\Models\Admin::where('user_id', $user->id)->first();
            if ($targetAdmin && $targetAdmin->admin_level === 'super') {
                return redirect('/admin/users')->withErrors(['msg' => 'Anda tidak memiliki akses untuk mengupdate Super Admin.']);
            }
        }

        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'jenis_kelamin' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'no_telepon' => 'required|string',
            'alamat' => 'nullable|string',
            'alamat' => 'nullable|string',
            'jurusan' => 'required_if:role,mahasiswa,dosen|nullable|string',
            'fakultas' => 'required_if:role,dosen|nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                // 1. Update User
                $userData = [
                    'nama' => $request->nama,
                    'email' => $request->email,
                ];
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                $user->update($userData);

                // 2. Update Role Detail
                if ($user->role === 'admin') {
                    Admin::where('user_id', $user->id)->update([
                        'nama' => $request->nama,
                        'email' => $request->email,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'no_telepon' => $request->no_telepon,
                        'alamat' => $request->alamat,
                    ]);
                } elseif ($user->role === 'dosen') {
                    Dosen::where('user_id', $user->id)->update([
                        'nama' => $request->nama,
                        'email' => $request->email,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'no_telepon' => $request->no_telepon,
                        'fakultas' => $request->fakultas,
                        'fakultas' => $request->fakultas,
                        'jurusan' => $request->jurusan,
                        'alamat' => $request->alamat,
                    ]);
                } elseif ($user->role === 'mahasiswa') {
                    Mahasiswa::where('user_id', $user->id)->update([
                        'nama' => $request->nama,
                        'email' => $request->email,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'no_telepon' => $request->no_telepon,
                        'jurusan' => $request->jurusan,
                        'alamat' => $request->alamat,
                    ]);
                }
            });

            $redirectPath = match($user->role) {
                'admin' => '/admin/admin-data',
                'dosen' => '/admin/dosen',
                'mahasiswa' => '/admin/mahasiswa',
                default => '/admin/dashboard',
            };

            return redirect($redirectPath)->with('success', 'User berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Check if current admin is second admin trying to delete super admin
        $currentAdmin = \App\Models\Admin::where('user_id', auth()->id())->first();
        if ($currentAdmin && $currentAdmin->admin_level === 'second' && $user->role === 'admin') {
            $targetAdmin = \App\Models\Admin::where('user_id', $user->id)->first();
            if ($targetAdmin && $targetAdmin->admin_level === 'super') {
                return redirect('/admin/users')->withErrors(['msg' => 'Anda tidak memiliki akses untuk menghapus Super Admin.']);
            }
        }
        
        if ($user->id == auth()->id()) {
            return back()->withErrors(['msg' => 'Tidak dapat menghapus akun sendiri.']);
        }

        try {
            DB::transaction(function () use ($user) {
                if ($user->role === 'admin') {
                    \App\Models\Admin::where('user_id', $user->id)->delete();
                } elseif ($user->role === 'dosen') {
                    Dosen::where('user_id', $user->id)->delete();
                } elseif ($user->role === 'mahasiswa') {
                    Mahasiswa::where('user_id', $user->id)->delete();
                }
                $user->delete();
            });

            $redirectPath = match($user->role) {
                'admin' => '/admin/admin-data',
                'dosen' => '/admin/dosen',
                'mahasiswa' => '/admin/mahasiswa',
                default => '/admin/dashboard',
            };

            return redirect($redirectPath)->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }
}
