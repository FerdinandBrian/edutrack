<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'mahasiswa') {
            // Ambil data jadwal dari DKBS mahasiswa yang login
            $data = \App\Models\Dkbs::with(['perkuliahan.mataKuliah', 'perkuliahan.dosen', 'perkuliahan.ruangan'])
                ->where('nrp', $user->identifier)
                ->get();
        } else {
            // Existing logic for other roles (admin/dosen uses manual Jadwal entries usually, or generic)
            try {
                $data = Jadwal::with('dosen')->get();
            } catch (\Exception $e) {
                Log::error('Jadwal index error: '. $e->getMessage());
                session()->flash('error', 'Terjadi masalah saat memuat jadwal. Cek log.');
                $data = collect();
            }
        }

        return view($user->role . '.jadwal.index', compact('data'));
    }

    public function create()
    {
        $dosens = Dosen::orderBy('nama')->get();
        return view('dosen.jadwal.create', compact('dosens'));
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
        
        $user = auth()->user();
        return redirect('/' . $user->role . '/jadwal')->with('success','Jadwal tersimpan');
    }

    public function show($id)
    {
        $user = auth()->user();
        
        if ($user->role === 'mahasiswa') {
            try {
                // For mahasiswa, $id is the DKBS id
                $row = \App\Models\Dkbs::with(['perkuliahan.mataKuliah', 'perkuliahan.dosen', 'perkuliahan.ruangan'])
                    ->where('nrp', $user->identifier)
                    ->findOrFail($id);
            } catch (\Exception $e) {
                 return redirect('/mahasiswa/jadwal')->with('error','Data jadwal tidak ditemukan.');
            }
        } else {
            try {
                $row = Jadwal::with('dosen')->findOrFail($id);
            } catch (\Exception $e) {
                Log::error('Jadwal show error: '. $e->getMessage());
                return redirect('/' . $user->role . '/jadwal')->with('error','Data jadwal tidak ditemukan atau terjadi masalah.');
            }
        }

        return view($user->role . '.jadwal.show', compact('row'));
    }

    public function edit(Jadwal $jadwal)
    {
        $dosens = Dosen::orderBy('nama')->get();
        return view('dosen.jadwal.edit', compact('jadwal','dosens'));
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
        
        $user = auth()->user();
        return redirect('/' . $user->role . '/jadwal')->with('success','Jadwal diperbarui');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return back()->with('success','Jadwal dihapus');
    }
}
