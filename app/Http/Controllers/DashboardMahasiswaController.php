<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardMahasiswaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa; // relasi

        return view('dashboard.mahasiswa', [
            'user' => $user,
            'mahasiswa' => $mahasiswa,
        ]);
    }
}