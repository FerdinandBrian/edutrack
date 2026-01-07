<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index()
    {
        $data = MataKuliah::orderBy('jurusan', 'asc')->orderBy('nama_mk', 'asc')->get();
        return view('admin.mata_kuliah.index', compact('data'));
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
        ]);

        MataKuliah::create($validated);
        return redirect('/admin/mata-kuliah')->with('success','Mata kuliah berhasil ditambahkan.');
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
        ]);

        $mk->update($validated);
        return redirect('/admin/mata-kuliah')->with('success','Mata kuliah berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        MataKuliah::where('kode_mk', $id)->delete();
        return back()->with('success','Mata kuliah berhasil dihapus.');
    }
}
