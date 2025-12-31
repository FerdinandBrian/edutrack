<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        return view($role . '.profile', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        $role = $user->role;

        return view($role . '.profile_edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:255',
        ]);

        // Update User Table
        $user->update([
            'nama' => $request->nama,
            'email' => $request->email,
        ]);

        // Update Role Table
        $roleData = [
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
             // Syncing nama & email to role table as well (if columns exist)
            'nama' => $request->nama,
            'email' => $request->email,
        ];

        if ($role === 'mahasiswa') {
            $user->mahasiswa()->update($roleData);
        } elseif ($role === 'dosen') {
             // Dosen model has specific fields, verify if these match
            $user->dosen()->update($roleData);
        } elseif ($role === 'admin') {
            $user->admin()->update($roleData);
        }

        return redirect('/' . $role . '/profile')->with('success', 'Profil berhasil diperbaharui!');
    }
}
