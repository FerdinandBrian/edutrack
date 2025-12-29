<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardMahasiswaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa; // relasi

        return view('mahasiswa.dashboard', [
            'user' => $user,
            'mahasiswa' => $mahasiswa,
        ]);
    }
}