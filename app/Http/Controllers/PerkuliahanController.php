<?php

namespace App\Http\Controllers;

use App\Models\Perkuliahan;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $typeRank = stripos($name, 'Praktikum') !== false ? 1 : 0; // 0 for Teori, 1 for Praktikum
                
                return sprintf('%s|%s|%s|%d', $jurusan, $baseName, $item->kelas, $typeRank);
            });

        return view('admin.perkuliahan.index', compact('data'));
    }

    public function create()
    {
        $mataKuliahs = MataKuliah::orderBy('nama_mk')->get();
        $dosens = Dosen::orderBy('nama')->get();
        $ruangans = Ruangan::all();
        $jurusans = Dosen::select('jurusan')->distinct()->whereNotNull('jurusan')->orderBy('jurusan')->pluck('jurusan');
        return view('admin.perkuliahan.create', compact('mataKuliahs', 'dosens', 'ruangans', 'jurusans'));
    }

    public function store(Request $request)
    {
        // Auto-calculate End Time based on SKS (1 SKS = 50 minutes)
        if ($request->has('kode_mk') && $request->has('jam_mulai')) {
             $mk = MataKuliah::where('kode_mk', $request->kode_mk)->first();
             if ($mk) {
                 // Check if it's a Praktikum class (case-insensitive + kode_mk suffix check)
                 $isPraktikum = stripos($mk->nama_mk, 'Praktikum') !== false || str_ends_with($mk->kode_mk, 'P');
                 
                 if ($isPraktikum) {
                     $minutes = 120; // 2 hours for Praktikum
                 } else {
                     $minutes = $mk->sks * 50;
                 }
                 $endTime = \Carbon\Carbon::parse($request->jam_mulai)->addMinutes($minutes)->format('H:i');
                 $request->merge(['jam_berakhir' => $endTime]);
             }
        }

        $validated = $request->validate([
            'kode_mk' => 'required|string|exists:mata_kuliah,kode_mk',
            'nip_dosen' => 'required|string|exists:dosen,nip',
            'kode_ruangan' => 'required|string',
            'kelas' => 'required|string|max:10',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_berakhir' => 'required',
            'tahun_ajaran' => 'required|string',
            'kapasitas' => 'required|numeric|min:1',
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


        // Ensure Room Exists and Update Capacity
        Ruangan::updateOrCreate(
            ['kode_ruangan' => $validated['kode_ruangan']],
            [
                'nama_ruangan' => 'Ruang ' . $validated['kode_ruangan'],
                'kapasitas' => $validated['kapasitas']
            ]
        );

        Perkuliahan::create($validated);

        // Auto-create Praktikum class if this is a Teori class
        $mk = MataKuliah::where('kode_mk', $request->kode_mk)->first();
        if ($mk && (stripos($mk->nama_mk, '(Teori)') !== false || str_ends_with($mk->kode_mk, 'T'))) {
            // Find the paired Praktikum course
            $baseName = trim(str_ireplace(['(Teori)', 'Teori'], '', $mk->nama_mk));
            $praktikumMk = MataKuliah::where(function($query) use ($baseName, $mk) {
                    $query->where('nama_mk', 'LIKE', $baseName . '%Praktikum%')
                          ->orWhere('kode_mk', 'LIKE', substr($mk->kode_mk, 0, -1) . 'P');
                })
                ->where('jurusan', $mk->jurusan)
                ->first();

            if ($praktikumMk) {
                // Calculate Praktikum schedule: 30 minutes after Teori ends
                $teoriEnd = \Carbon\Carbon::parse($request->jam_berakhir);
                $praktikumStart = $teoriEnd->copy()->addMinutes(30);
                $praktikumEnd = $praktikumStart->copy()->addMinutes(120); // 2 hours for auto-created Praktikum

                // Create Praktikum class automatically
                // Ensure Room Exists for auto-created practicum too
                Ruangan::updateOrCreate(
                    ['kode_ruangan' => $request->kode_ruangan],
                    [
                        'nama_ruangan' => 'Ruang ' . $request->kode_ruangan,
                        'kapasitas' => $request->kapasitas
                    ]
                );

                Perkuliahan::create([
                    'kode_mk' => $praktikumMk->kode_mk,
                    'nip_dosen' => $request->nip_dosen,
                    'kode_ruangan' => $request->kode_ruangan,
                    'kelas' => $request->kelas,
                    'hari' => $request->hari,
                    'jam_mulai' => $praktikumStart->format('H:i'),
                    'jam_berakhir' => $praktikumEnd->format('H:i'),
                    'tahun_ajaran' => $request->tahun_ajaran,
                ]);
            }
        }

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
                 // Check if it's a Praktikum class (case-insensitive + kode_mk suffix check)
                 $isPraktikum = stripos($mk->nama_mk, 'Praktikum') !== false || str_ends_with($mk->kode_mk, 'P');
                 
                 if ($isPraktikum) {
                     $minutes = 120; // 2 hours for Praktikum
                 } else {
                     $minutes = $mk->sks * 50;
                 }
                 $endTime = \Carbon\Carbon::parse($request->jam_mulai)->addMinutes($minutes)->format('H:i');
                 $request->merge(['jam_berakhir' => $endTime]);
             }
        }

        $validated = $request->validate([
            'kode_mk' => 'required|string|exists:mata_kuliah,kode_mk',
            'nip_dosen' => 'required|string|exists:dosen,nip',
            'kode_ruangan' => 'required|string',
            'kelas' => 'required|string|max:10',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_berakhir' => 'required',
            'tahun_ajaran' => 'required|string',
            'kapasitas' => 'required|numeric|min:1',
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
        
        // Ensure Room Exists and Update Capacity
        Ruangan::updateOrCreate(
            ['kode_ruangan' => $validated['kode_ruangan']],
            [
                'nama_ruangan' => 'Ruang ' . $validated['kode_ruangan'],
                'kapasitas' => $validated['kapasitas']
            ]
        );
        
        $perkuliahan->update($validated);
        return redirect('/admin/perkuliahan')->with('success', 'Jadwal perkuliahan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        Perkuliahan::destroy($id);
        return back()->with('success', 'Jadwal perkuliahan berhasil dihapus.');
    }

    public function getDosenByJurusan(Request $request)
    {
        $jurusan = $request->query('jurusan');
        $dosens = Dosen::where('jurusan', $jurusan)->orderBy('nama')->get();
        return response()->json($dosens);
    }

    public function getMataKuliahByJurusan(Request $request)
    {
        $jurusan = $request->query('jurusan');
        try {
            $mataKuliahs = DB::select('CALL sp_get_matkul_by_jurusan(?)', [$jurusan]);
            return response()->json($mataKuliahs);
        } catch (\Exception $e) {
            // Fallback for robustness during development (optional)
            // return response()->json(\App\Models\MataKuliah::where('jurusan', $jurusan)->orderBy('semester')->orderBy('nama_mk')->get());
            
            return response()->json(['error' => $e->getMessage()], 200);
        }
    }
    public function statusKelas(Request $request)
    {
        $tahunAjaran = $request->query('tahun_ajaran', '2025/2026 - Ganjil'); // Default TA
        
        // Ambil semua TA yang unik untuk filter dropdown
        $allTa = DB::table('perkuliahan')
            ->select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        try {
            $statusData = DB::select('CALL sp_cek_status_kelas(?)', [$tahunAjaran]);
            
            // Map the plain objects to include course details for grouping/sorting
            // Since SP might not return jurusan, we fetch and merge or just sort in PHP
            $data = collect($statusData)->map(function($item) {
                $mk = DB::table('mata_kuliah')->where('kode_mk', $item->kode_mk)->first();
                $item->jurusan = $mk->jurusan ?? 'Umum';
                $item->sks = $mk->sks ?? 0;
                
                // Ambil kapasitas aslinya dari tabel ruangan jika SP belum me-return
                if (!isset($item->kapasitas)) {
                    $item->kapasitas = DB::table('perkuliahan')
                        ->join('ruangan', 'perkuliahan.kode_ruangan', '=', 'ruangan.kode_ruangan')
                        ->where('id_perkuliahan', $item->id_perkuliahan)
                        ->value('ruangan.kapasitas') ?? 0;
                }
                
                return $item;
            })->sortBy(function($item) {
                $baseName = trim(str_replace(['(Teori)', '(Praktikum)'], '', $item->nama_mk));
                $typeRank = stripos($item->nama_mk, 'Praktikum') !== false ? 1 : 0;
                return sprintf('%s|%s|%s|%d', $item->jurusan, $baseName, $item->kelas, $typeRank);
            });

            return view('admin.perkuliahan.status', compact('data', 'tahunAjaran', 'allTa'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memanggil stored procedure: ' . $e->getMessage());
        }
    }
}
