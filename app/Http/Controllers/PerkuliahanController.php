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
        $data = Perkuliahan::with(['mataKuliah', 'dosen', 'ruangan'])
            ->get()
            ->sortBy(function($item) {
                // Sorting Logic:
                // 1. Jurusan
                // 2. Base Name (Algorithm name without Teori/Praktikum suffix)
                // 3. Class (A, B, C...)
                // 4. Type (Teori first, then Praktikum)
                
                $jurusan = $item->mataKuliah->jurusan ?? 'Umum';
                $name = $item->mataKuliah->nama_mk;
                $baseName = trim(str_replace(['(Teori)', '(Praktikum)'], '', $name));
                $typeRank = str_contains($name, 'Praktikum') ? 1 : 0; // 0 for Teori, 1 for Praktikum
                
                return sprintf('%s|%s|%s|%d', $jurusan, $baseName, $item->kelas, $typeRank);
            });

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
        // Auto-calculate End Time based on SKS (1 SKS = 50 minutes)
        if ($request->has('kode_mk') && $request->has('jam_mulai')) {
             $mk = MataKuliah::where('kode_mk', $request->kode_mk)->first();
             if ($mk) {
                 $minutes = $mk->sks * 50;
                 $endTime = \Carbon\Carbon::parse($request->jam_mulai)->addMinutes($minutes)->format('H:i');
                 $request->merge(['jam_berakhir' => $endTime]);
             }
        }

        $validated = $request->validate([
            'kode_mk' => 'required|string|exists:mata_kuliah,kode_mk',
            'nip_dosen' => 'required|string|exists:dosen,nip',
            'kode_ruangan' => 'required|string|exists:ruangan,kode_ruangan',
            'kelas' => 'required|string|max:10',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_berakhir' => 'required',
            'tahun_ajaran' => 'required|string',
        ]);

        // Check for Room Collision
        $collision = Perkuliahan::where('kode_ruangan', $request->kode_ruangan)
            ->where('hari', $request->hari)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->where(function($q) use ($request) {
                $q->where(function($inner) use ($request) {
                    $inner->where('jam_mulai', '<', $request->jam_berakhir)
                          ->where('jam_berakhir', '>', $request->jam_mulai);
                });
            })->first();

        if ($collision) {
            return back()->withInput()->withErrors(['msg' => "Bentrok! Ruangan {$request->kode_ruangan} sudah digunakan di hari {$request->hari} pada jam tersebut."]);
        }

        // Check for Dosen Collision
        $dosenCollision = Perkuliahan::where('nip_dosen', $request->nip_dosen)
            ->where('hari', $request->hari)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->where(function($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_berakhir)
                  ->where('jam_berakhir', '>', $request->jam_mulai);
            })->first();

        if ($dosenCollision) {
            return back()->withInput()->withErrors(['msg' => "Bentrok! Dosen tersebut sudah memiliki jadwal mengajar di jam tersebut."]);
        }

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

        // Auto-calculate End Time based on SKS (1 SKS = 50 minutes)
        if ($request->has('kode_mk') && $request->has('jam_mulai')) {
             $mk = MataKuliah::where('kode_mk', $request->kode_mk)->first();
             if ($mk) {
                 $minutes = $mk->sks * 50;
                 $endTime = \Carbon\Carbon::parse($request->jam_mulai)->addMinutes($minutes)->format('H:i');
                 $request->merge(['jam_berakhir' => $endTime]);
             }
        }

        $validated = $request->validate([
            'kode_mk' => 'required|string|exists:mata_kuliah,kode_mk',
            'nip_dosen' => 'required|string|exists:dosen,nip',
            'kode_ruangan' => 'required|string|exists:ruangan,kode_ruangan',
            'kelas' => 'required|string|max:10',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_berakhir' => 'required',
            'tahun_ajaran' => 'required|string',
        ]);

        // Check for Room Collision (excluding current record)
        $collision = Perkuliahan::where('kode_ruangan', $request->kode_ruangan)
            ->where('hari', $request->hari)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->where('id_perkuliahan', '!=', $id)
            ->where(function($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_berakhir)
                  ->where('jam_berakhir', '>', $request->jam_mulai);
            })->first();

        if ($collision) {
            return back()->withInput()->withErrors(['msg' => "Bentrok! Ruangan {$request->kode_ruangan} sudah digunakan."]);
        }

        // Check for Dosen Collision (excluding current record)
        $dosenCollision = Perkuliahan::where('nip_dosen', $request->nip_dosen)
            ->where('hari', $request->hari)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->where('id_perkuliahan', '!=', $id)
            ->where(function($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_berakhir)
                  ->where('jam_berakhir', '>', $request->jam_mulai);
            })->first();

        if ($dosenCollision) {
            return back()->withInput()->withErrors(['msg' => "Bentrok! Dosen tersebut sudah memiliki jadwal mengajar di jam tersebut."]);
        }
        
        $perkuliahan->update($validated);
        return redirect('/admin/perkuliahan')->with('success', 'Jadwal perkuliahan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        Perkuliahan::destroy($id);
        return back()->with('success', 'Jadwal perkuliahan berhasil dihapus.');
    }
}
