<?php

namespace App\Http\Controllers;

use App\Models\Dkbs;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Perkuliahan;
use Illuminate\Http\Request;

class DkbsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Dkbs::with(['mahasiswa', 'mataKuliah', 'perkuliahan.ruangan', 'perkuliahan.dosen']);

        // Filtering by tahun_ajaran
        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        // Searching by NRP or Student Name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nrp', 'like', "%$search%")
                  ->orWhereHas('mahasiswa', function($sq) use ($search) {
                      $sq->where('nama', 'like', "%$search%");
                  });
            });
        }

        if ($user->role === 'mahasiswa') {
            $data = $query->where('nrp', $user->identifier)->get();

            // Custom sorting for Days
            $dayOrder = [
                'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7
            ];

            $data = $data->sort(function($a, $b) use ($dayOrder) {
                $dayA = $a->perkuliahan->hari ?? '';
                $dayB = $b->perkuliahan->hari ?? '';
                
                $valA = $dayOrder[$dayA] ?? 99;
                $valB = $dayOrder[$dayB] ?? 99;

                if ($valA === $valB) {
                    return strcmp($a->perkuliahan->jam_mulai ?? '', $b->perkuliahan->jam_mulai ?? '');
                }
                return $valA <=> $valB;
            });
        } else {
            $data = $query->get();
        }

        $tahun_ajarans = Dkbs::distinct()->pluck('tahun_ajaran');

        return view($user->role . '.dkbs.index', compact('data', 'tahun_ajarans'));
    }

    public function create()
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        
        $fixedPeriods = [
            '2024/2025 - Ganjil', '2024/2025 - Genap',
            '2025/2026 - Ganjil', '2025/2026 - Genap',
            '2026/2027 - Ganjil', '2026/2027 - Genap',
            '2027/2028 - Ganjil', '2027/2028 - Genap'
        ];
        
        $dbPeriods = Perkuliahan::distinct()->pluck('tahun_ajaran')->toArray();
        $periods = array_values(array_unique(array_merge($dbPeriods, $fixedPeriods)));
        sort($periods);

        return view('admin.dkbs.create', compact('mahasiswas', 'periods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nrp' => 'required',
            'id_perkuliahan' => 'required|exists:perkuliahan,id_perkuliahan',
            'status' => 'nullable|string'
        ]);

        $perkuliahan = Perkuliahan::with('ruangan')->findOrFail($request->id_perkuliahan);

        // 1. Capacity Check
        $currentEnrolled = Dkbs::where('id_perkuliahan', $perkuliahan->id_perkuliahan)->count();
        $capacity = $perkuliahan->ruangan ? $perkuliahan->ruangan->kapasitas : 40; // Default limit if no room assigned

        if ($currentEnrolled >= $capacity) {
             return back()->withErrors(['msg' => "Kelas Penuh! Kapasitas ruangan hanya $capacity kursi, dan sudah terisi $currentEnrolled mahasiswa."])->withInput();
        }

        // 2. Duplication Check - Check if student already enrolled in THIS specific class
        $exists = Dkbs::where('nrp', $request->nrp)
            ->where('id_perkuliahan', $request->id_perkuliahan)
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Mahasiswa sudah terdaftar di kelas ini.'])->withInput();
        }

        Dkbs::create([
            'nrp' => $request->nrp,
            'id_perkuliahan' => $request->id_perkuliahan,
            'kode_mk' => $perkuliahan->kode_mk,
            'tahun_ajaran' => $perkuliahan->tahun_ajaran,
            'status' => $request->status ?? 'Terdaftar',
            'semester' => $perkuliahan->mataKuliah->semester
        ]);

        // 3. Auto-enroll in paired class (Teori <-> Praktikum)
        $pairedKodeMk = null;
        $baseMk = substr($perkuliahan->kode_mk, 0, -1); // Remove last character (T or P)
        $lastChar = substr($perkuliahan->kode_mk, -1); // Get T or P

        if ($lastChar === 'T') {
            $pairedKodeMk = $baseMk . 'P'; // Find Praktikum
        } elseif ($lastChar === 'P') {
            $pairedKodeMk = $baseMk . 'T'; // Find Teori
        }

        if ($pairedKodeMk) {
            // Find the paired class with same kelas letter
            $pairedPerkuliahan = Perkuliahan::with('ruangan', 'mataKuliah')
                ->where('kode_mk', $pairedKodeMk)
                ->where('kelas', $perkuliahan->kelas)
                ->where('tahun_ajaran', $perkuliahan->tahun_ajaran)
                ->first();

            if ($pairedPerkuliahan) {
                // Check capacity for paired class
                $pairedEnrolled = Dkbs::where('id_perkuliahan', $pairedPerkuliahan->id_perkuliahan)->count();
                $pairedCapacity = $pairedPerkuliahan->ruangan ? $pairedPerkuliahan->ruangan->kapasitas : 40;

                // Check if not already enrolled in paired class
                $pairedExists = Dkbs::where('nrp', $request->nrp)
                    ->where('id_perkuliahan', $pairedPerkuliahan->id_perkuliahan)
                    ->exists();

                if (!$pairedExists && $pairedEnrolled < $pairedCapacity) {
                    // Auto-enroll in paired class
                    Dkbs::create([
                        'nrp' => $request->nrp,
                        'id_perkuliahan' => $pairedPerkuliahan->id_perkuliahan,
                        'kode_mk' => $pairedPerkuliahan->kode_mk,
                        'tahun_ajaran' => $pairedPerkuliahan->tahun_ajaran,
                        'status' => $request->status ?? 'Terdaftar',
                        'semester' => $pairedPerkuliahan->mataKuliah->semester
                    ]);
                }
            }
        }

        return redirect('/admin/dkbs')->with('success', 'Mahasiswa berhasil didaftarkan ke kelas' . ($pairedKodeMk && $pairedPerkuliahan ? ' (termasuk pasangan Teori/Praktikum)' : ''));
    }

    public function edit(Dkbs $dkbs)
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        
        $fixedPeriods = [
            '2024/2025 - Ganjil', '2024/2025 - Genap',
            '2025/2026 - Ganjil', '2025/2026 - Genap',
            '2026/2027 - Ganjil', '2026/2027 - Genap',
            '2027/2028 - Ganjil', '2027/2028 - Genap'
        ];

        $dbPeriods = Perkuliahan::distinct()->pluck('tahun_ajaran')->toArray();
        $periods = array_values(array_unique(array_merge($dbPeriods, $fixedPeriods)));
        sort($periods);

        return view('admin.dkbs.edit', compact('dkbs','mahasiswas', 'periods'));
    }

    public function update(Request $request, Dkbs $dkbs)
    {
        $request->validate([
            'nrp' => 'required',
            'id_perkuliahan' => 'required|exists:perkuliahan,id_perkuliahan',
            'status' => 'nullable|string'
        ]);

        $perkuliahan = Perkuliahan::findOrFail($request->id_perkuliahan);

        // Duplication Check (excluding self)
        $exists = Dkbs::where('nrp', $request->nrp)
            ->where('kode_mk', $perkuliahan->kode_mk)
            ->where('tahun_ajaran', $perkuliahan->tahun_ajaran)
            ->where('id', '!=', $dkbs->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Data duplikat terdeteksi.'])->withInput();
        }

        $dkbs->update([
            'nrp' => $request->nrp,
            'id_perkuliahan' => $request->id_perkuliahan,
            'kode_mk' => $perkuliahan->kode_mk,
            'tahun_ajaran' => $perkuliahan->tahun_ajaran,
            'status' => $request->status,
            'semester' => $perkuliahan->mataKuliah->semester
        ]);
        
        return redirect('/admin/dkbs')->with('success','DKBS diperbarui');
    }

    public function destroy(Dkbs $dkbs)
    {
        $dkbs->delete();
        return back()->with('success','DKBS dihapus');
    }

    public function getPerkuliahanByTahunAjaran(Request $request)
    {
        $ta = $request->tahun_ajaran;
        $data = Perkuliahan::with('mataKuliah')
            ->where('tahun_ajaran', $ta)
            ->get()
            ->filter(function($p) {
                // Only show Theory classes (ending with T) and standalone classes (not ending with P)
                // Hide Praktikum classes since they will be auto-enrolled
                $lastChar = substr($p->kode_mk, -1);
                return $lastChar !== 'P'; // Exclude all Praktikum classes
            })
            ->sortBy([
                ['mataKuliah.nama_mk', 'asc'],  // Sort by course name first
                ['kelas', 'asc']                 // Then by class (A, B, C)
            ])
            ->map(function($p) {
                return [
                    'id' => $p->id_perkuliahan,
                    'label' => "[{$p->kode_mk}] {$p->mataKuliah->nama_mk} - Kelas {$p->kelas}"
                ];
            })
            ->values(); // Re-index array after filter and sort
            
        return response()->json($data);
    }

    public function getMataKuliahBySemester($semester)
    {
        $courses = MataKuliah::where('semester', $semester)->get();
        return response()->json($courses);
    }
}
