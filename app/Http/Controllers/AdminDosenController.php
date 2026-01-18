<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;

class AdminDosenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dosen::query();

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        $dosens = $query->orderBy('jurusan', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        $jurusans = Dosen::select('jurusan')->distinct()->whereNotNull('jurusan')->orderBy('jurusan')->pluck('jurusan');

        return view('admin.dosen.index', compact('dosens', 'jurusans'));
    }
}
