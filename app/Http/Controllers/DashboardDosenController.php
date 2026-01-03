<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardDosenController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $dosen = $user->dosen; // relasi
        $nip = $user->identifier;

        // Get latest tahun ajaran from perkuliahan
        $activeYear = \App\Models\Perkuliahan::orderBy('tahun_ajaran', 'desc')->first()->tahun_ajaran ?? '2025/2026 - Ganjil';

        // Count classes taught by this lecturer in the active year
        $perkuliahanIds = \App\Models\Perkuliahan::where('nip_dosen', $nip)
            ->where('tahun_ajaran', $activeYear)
            ->pluck('id_perkuliahan');
        
        $totalKelas = $perkuliahanIds->count();

        // Count unique students enrolled in those classes
        $totalMahasiswa = \App\Models\Dkbs::whereIn('id_perkuliahan', $perkuliahanIds)
            ->distinct('nrp')
            ->count('nrp');

        return view('dosen.dashboard', [
            'user' => $user,
            'dosen' => $dosen,
            'totalKelas' => $totalKelas,
            'totalMahasiswa' => $totalMahasiswa,
            'activeYear' => $activeYear
        ]);
    }
}