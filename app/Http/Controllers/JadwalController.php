<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;

class JadwalController extends Controller
{
    public function index()
    {
        return Jadwal::with('dosen')->get();
    }
}
