<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $query = MataKuliah::query();

        // Filter by Jurusan
        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        // Filter by Sifat
        if ($request->filled('sifat')) {
            $query->where('sifat', $request->sifat);
        }

        // Filter by Semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $data = $query->orderBy('jurusan', 'asc')
            ->orderByRaw("
                CASE 
                    WHEN semester REGEXP '^[0-9]+$' THEN CAST(semester AS UNSIGNED)
                    WHEN semester = 'Ganjil' THEN 9
                    WHEN semester = 'Genap' THEN 10
                    ELSE 11
                END ASC
            ")
            ->orderBy('nama_mk', 'asc')
            ->get();
            
        // Get unique Jurusan for filter dropdown
        $jurusans = MataKuliah::select('jurusan')->distinct()->orderBy('jurusan')->pluck('jurusan');
        
        // Get unique Semester for filter dropdown
        $semesters = MataKuliah::select('semester')
            ->distinct()
            ->orderByRaw("
                CASE 
                    WHEN semester REGEXP '^[0-9]+$' THEN CAST(semester AS UNSIGNED)
                    WHEN semester = 'Ganjil' THEN 9
                    WHEN semester = 'Genap' THEN 10
                    ELSE 11
                END ASC
            ")
            ->pluck('semester');

        return view('admin.mata_kuliah.index', compact('data', 'jurusans', 'semesters'));
    }

    public function create()
    {
        return view('admin.mata_kuliah.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|unique:mata_kuliah,kode_mk',
            'nama_mk' => 'required|string',
            'jurusan' => 'required|string',
            'sks' => 'required|integer|min:1|max:8',
            'semester' => 'required|integer|min:1|max:8',
            'sifat' => 'required|in:Wajib,Pilihan',
        ]);

        MataKuliah::create($validated);
        return redirect('/admin/mata-kuliah')->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $mk = MataKuliah::findOrFail($id);
        return view('admin.mata_kuliah.edit', compact('mk'));
    }

    public function update(Request $request, string $id)
    {
        $mk = MataKuliah::findOrFail($id);

        $validated = $request->validate([
            'nama_mk' => 'required|string',
            'jurusan' => 'required|string',
            'sks' => 'required|integer|min:1|max:8',
            'semester' => 'required|integer|min:1|max:8',
            'sifat' => 'required|in:Wajib,Pilihan',
        ]);

        $mk->update($validated);
        return redirect('/admin/mata-kuliah')->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        MataKuliah::where('kode_mk', $id)->delete();
        return back()->with('success', 'Mata kuliah berhasil dihapus.');
    }
}