<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class AdminMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::query();

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        $mahasiswas = $query->orderBy('jurusan', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        $jurusans = Mahasiswa::select('jurusan')->distinct()->whereNotNull('jurusan')->orderBy('jurusan')->pluck('jurusan');

        return view('admin.mahasiswa.index', compact('mahasiswas', 'jurusans'));
    }
}
