<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function index()
    {
        $nilai = Nilai::with('mahasiswa')->get();
        return view('nilai.index', compact('nilai'));
    }
}

