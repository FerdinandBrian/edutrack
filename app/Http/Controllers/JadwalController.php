<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Dosen;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        try {
            $data = Jadwal::with('dosen')->get();
        } catch (\Exception $e) {
            \Log::error('Jadwal index error: '. $e->getMessage());
            session()->flash('error', 'Terjadi masalah saat memuat jadwal. Cek log.');
            $data = collect();
        }

        return view('jadwal.index', compact('data'));
    }

    public function create()
    {
        $dosens = Dosen::orderBy('nama')->get();
        return view('jadwal.create', compact('dosens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string',
            'nidn' => 'nullable|string',
            'hari' => 'required|string',
            'jam' => 'required|string',
        ]);

        Jadwal::create($validated);
        return redirect('/jadwal')->with('success','Jadwal tersimpan');
    }

    public function show($id)
    {
        try {
            $row = Jadwal::with('dosen')->findOrFail($id);
        } catch (\Exception $e) {
            \Log::error('Jadwal show error: '. $e->getMessage());
            return redirect('/jadwal')->with('error','Data jadwal tidak ditemukan atau terjadi masalah.');
        }

        return view('jadwal.show', compact('row'));
    }

    public function edit(Jadwal $jadwal)
    {
        $dosens = Dosen::orderBy('nama')->get();
        return view('jadwal.edit', compact('jadwal','dosens'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string',
            'nidn' => 'nullable|string',
            'hari' => 'required|string',
            'jam' => 'required|string',
        ]);

        $jadwal->update($validated);
        return redirect('/jadwal')->with('success','Jadwal diperbarui');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return back()->with('success','Jadwal dihapus');
    }
}
