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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $dosens = $query->orderBy('jurusan', 'asc')
            ->orderBy('nama', 'asc')
            ->get();

        $jurusans = Dosen::select('jurusan')->distinct()->whereNotNull('jurusan')->orderBy('jurusan')->pluck('jurusan');

        return view('admin.dosen.index', compact('dosens', 'jurusans'));
    }
}
