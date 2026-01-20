<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardMahasiswaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        // 1. Dapatkan Tahun Ajaran terbaru dari DKBS mahasiswa ini
        $latestDkbs = \App\Models\Dkbs::where('nrp', $mahasiswa->nrp)
            ->orderBy('tahun_ajaran', 'desc')
            ->first();

        $selectedTa = $latestDkbs ? $latestDkbs->tahun_ajaran : null;

        // 2. Hitung total SKS berdasarkan Tahun Ajaran TERBARU (bukan hardcode semester 3)
        $totalSks = 0;
        if ($selectedTa) {
            $totalSks = \App\Models\Dkbs::where('nrp', $mahasiswa->nrp)
                ->where('dkbs.tahun_ajaran', $selectedTa)
                ->join('mata_kuliah', 'dkbs.kode_mk', '=', 'mata_kuliah.kode_mk')
                ->sum('mata_kuliah.sks');
        }

        // 3. Menggunakan DATABASE FUNCTION untuk hitung IPK Real-time
        $ipk = DB::select("SELECT get_ipk(?) as ipk", [$user->identifier])[0]->ipk;

        $statusAkademik = $selectedTa ? "Aktif - " . $selectedTa : "N/A";

        return view('mahasiswa.dashboard', [
            'user' => $user,
            'mahasiswa' => $mahasiswa,
            'totalSks' => $totalSks,
            'statusAkademik' => $statusAkademik,
            'ipk' => $ipk ?? 0.00
        ]);
    }
}