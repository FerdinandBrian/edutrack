<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardDosenController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $dosen = $user->dosen; // relasi

        return view('dashboard.dosen', [
            'user' => $user,
            'dosen' => $dosen,
        ]);
    }
}