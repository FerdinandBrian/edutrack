<?php

namespace App\Http\Controllers;

use App\Models\Perkuliahan;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class PerkuliahanController extends Controller
{
    public function index()
    {
        $data = Perkuliahan::with(['mataKuliah', 'dosen', 'ruangan'])->get();
        return view('admin.perkuliahan.index', compact('data'));
    }

    public function create()
    {
        $mataKuliahs = MataKuliah::orderBy('nama_mk')->get();
        $dosens = Dosen::orderBy('nama')->get();
        $ruangans = Ruangan::all();
        return view('admin.perkuliahan.create', compact('mataKuliahs', 'dosens', 'ruangans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|exists:mata_kuliah,kode_mk',
            'nip_dosen' => 'required|string|exists:dosen,nip',
            'kode_ruangan' => 'required|string|exists:ruangan,kode_ruangan',
            'kelas' => 'required|string|max:10',
            'jam_mulai' => 'required',
            'jam_berakhir' => 'required',
            'tahun_ajaran' => 'required|string',
        ]);

        Perkuliahan::create($validated);
        return redirect('/admin/perkuliahan')->with('success', 'Jadwal perkuliahan berhasil dibuat.');
    }

    public function edit(string $id)
    {
        $perkuliahan = Perkuliahan::findOrFail($id);
        $mataKuliahs = MataKuliah::orderBy('nama_mk')->get();
        $dosens = Dosen::orderBy('nama')->get();
        $ruangans = Ruangan::all();
        return view('admin.perkuliahan.edit', compact('perkuliahan', 'mataKuliahs', 'dosens', 'ruangans'));
    }

    public function update(Request $request, string $id)
    {
        $perkuliahan = Perkuliahan::findOrFail($id);

        $validated = $request->validate([
            'kode_mk' => 'required|string|exists:mata_kuliah,kode_mk',
            'nip_dosen' => 'required|string|exists:dosen,nip',
            'kode_ruangan' => 'required|string|exists:ruangan,kode_ruangan',
            'kelas' => 'required|string|max:10',
            'jam_mulai' => 'required',
            'jam_berakhir' => 'required',
            'tahun_ajaran' => 'required|string',
        ]);

        $perkuliahan->update($validated);
        return redirect('/admin/perkuliahan')->with('success', 'Jadwal perkuliahan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        Perkuliahan::destroy($id);
        return back()->with('success', 'Jadwal perkuliahan berhasil dihapus.');
    }
}
