<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardMahasiswaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa; // relasi

        // Calculate total SKS from DKBS (Specifically Semester 3)
        $totalSks = \App\Models\Dkbs::where('nrp', $mahasiswa->nrp)
            ->where('dkbs.semester', 3)
            ->join('mata_kuliah', 'dkbs.kode_mk', '=', 'mata_kuliah.kode_mk')
            ->sum('mata_kuliah.sks');

        // Menggunakan DATABASE FUNCTION untuk menghitung IPK secara Real-time
        $ipk = DB::select("SELECT get_ipk(?) as ipk", [$user->identifier])[0]->ipk;

        // Fetch status akademik from latest DKBS
        $latestDkbs = \App\Models\Dkbs::where('nrp', $mahasiswa->nrp)
            ->orderBy('id', 'desc')
            ->first();
        $statusAkademik = $latestDkbs ? "Aktif - " . $latestDkbs->tahun_ajaran : "N/A";

        return view('mahasiswa.dashboard', [
            'user' => $user,
            'mahasiswa' => $mahasiswa,
            'totalSks' => $totalSks,
            'statusAkademik' => $statusAkademik,
            'ipk' => $ipk ?? 0.00
        ]);
    }
}