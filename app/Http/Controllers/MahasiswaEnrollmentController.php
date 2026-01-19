<?php

namespace App\Http\Controllers;

use App\Models\Dkbs;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Perkuliahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MahasiswaEnrollmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role !== 'mahasiswa') {
            abort(403);
        }

        $mahasiswa = Mahasiswa::where('nrp', $user->identifier)->firstOrFail();
        
        // Determine Current Academic Year and Semester info
        $activeTa = Perkuliahan::orderBy('tahun_ajaran', 'desc')->first()->tahun_ajaran ?? '2025/2026 - Ganjil';
        $parts = explode(' - ', $activeTa);
        $semesterType = $parts[1] ?? 'Ganjil';
        $isGanjil = str_contains($semesterType, 'Ganjil');

        // Calculate student's current semester based on NRP (e.g. 24... means 2024)
        $enrollYear = 2000 + (int)substr($mahasiswa->nrp, 0, 2);
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');
        
        // Dynamic semester calculation based on current date if needed, 
        // but for now we follow the TA's year
        $partsTA = explode('/', $parts[0]);
        $taStartYear = (int)$partsTA[0];
        
        $currentSemester = ($taStartYear - $enrollYear) * 2 + ($isGanjil ? 1 : 2);

        // Get student's current selections
        $existingDkbs = Dkbs::where('nrp', $mahasiswa->nrp)
            ->where('tahun_ajaran', $activeTa)
            ->pluck('id_perkuliahan')
            ->toArray();
        
        // Enrollment is locked if student already has DKBS entries for this TA
        $isEnrolled = count($existingDkbs) > 0;

        // Fetch Courses for this student's major and semester
        $allCourses = MataKuliah::where('jurusan', $mahasiswa->jurusan)
            ->where(function($q) use ($currentSemester, $semesterType) {
                $q->where('semester', (string)$currentSemester)
                  ->orWhere('semester', $semesterType);
            })
            ->get();

        // Fetch available classes for each course in the current TA
        $availableClasses = Perkuliahan::with(['dosen', 'ruangan'])
            ->where('tahun_ajaran', $activeTa)
            ->whereIn('kode_mk', $allCourses->pluck('kode_mk'))
            ->get()
            ->groupBy('kode_mk');

        // Filter courses to only those that have available classes in this TA
        $courses = $allCourses->filter(function($course) use ($availableClasses) {
            return $availableClasses->has($course->kode_mk) && $availableClasses->get($course->kode_mk)->count() > 0;
        });

        // Calculate current SKS and enrolled data
        $totalSks = 0;
        $enrolledClasses = collect();
        if ($isEnrolled) {
            $enrolledPerkuliahan = Perkuliahan::with(['dosen', 'ruangan', 'mataKuliah'])
                ->whereIn('id_perkuliahan', $existingDkbs)
                ->get();
            
            // Sort by day and time
            $dayOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
            $enrolledClasses = $enrolledPerkuliahan->sort(function($a, $b) use ($dayOrder) {
                $da = $dayOrder[$a->hari] ?? 99;
                $db = $dayOrder[$b->hari] ?? 99;
                if ($da === $db) {
                    return $a->jam_mulai <=> $b->jam_mulai;
                }
                return $da <=> $db;
            });

            $totalSks = $enrolledClasses->sum(fn($p) => $p->mataKuliah->sks ?? 0);
        }

        return view('mahasiswa.enrollment.index', compact(
            'mahasiswa', 
            'courses', 
            'availableClasses', 
            'existingDkbs', 
            'activeTa', 
            'currentSemester',
            'totalSks',
            'isEnrolled',
            'enrolledClasses'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = Mahasiswa::where('nrp', $user->identifier)->firstOrFail();
        $activeTa = Perkuliahan::orderBy('tahun_ajaran', 'desc')->first()->tahun_ajaran ?? '2025/2026 - Ganjil';

        // Check if already enrolled to prevent resubmission
        $existingEnrollmentCount = Dkbs::where('nrp', $mahasiswa->nrp)
            ->where('tahun_ajaran', $activeTa)
            ->count();

        if ($existingEnrollmentCount > 0) {
            return back()->withErrors(['msg' => 'Anda sudah melakukan pendaftaran untuk semester ini. Pendaftaran hanya dapat dilakukan satu kali.'])->withInput();
        }

        $selections = $request->input('selections', []); // Array of id_perkuliahan
        
        // 1. Validate mandatory courses
        // Fetch courses for this student's major and semester
        $parts = explode(' - ', $activeTa);
        $years = explode('/', $parts[0]);
        $currentYear = (int)$years[0];
        $isGanjil = str_contains($parts[1], 'Ganjil');
        $enrollYear = 2000 + (int)substr($mahasiswa->nrp, 0, 2);
        $currentSemester = ($currentYear - $enrollYear) * 2 + ($isGanjil ? 1 : 2);

        $allMandatory = MataKuliah::where('jurusan', $mahasiswa->jurusan)
            ->where('sifat', 'Wajib')
            ->where(function($q) use ($currentSemester, $isGanjil) {
                $q->where('semester', (string)$currentSemester)
                  ->orWhere('semester', $isGanjil ? 'Ganjil' : 'Genap');
            })
            ->get();

        // Filter mandatory to those that have schedules in this TA
        $mandatoryCourses = $allMandatory->filter(function($mk) use ($activeTa) {
            return Perkuliahan::where('kode_mk', $mk->kode_mk)
                ->where('tahun_ajaran', $activeTa)
                ->exists();
        })->pluck('kode_mk')->toArray();

        $selectedPerkuliahan = Perkuliahan::whereIn('id_perkuliahan', $selections)->get();
        $selectedKodeMk = $selectedPerkuliahan->pluck('kode_mk')->toArray();

        foreach ($mandatoryCourses as $kodeMk) {
            if (!in_array($kodeMk, $selectedKodeMk)) {
                $mk = MataKuliah::find($kodeMk);
                return back()->withErrors(['msg' => "Mata kuliah wajib '{$mk->nama_mk}' harus dipilih."])->withInput();
            }
        }

        // 2. Conflict Check (Internal to the selection)
        // Sort by day and time to check overlaps
        $sortedSelections = $selectedPerkuliahan->sort(function($a, $b) {
            $dayOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];
            $valA = $dayOrder[$a->hari] ?? 99;
            $valB = $dayOrder[$b->hari] ?? 99;
            if ($valA === $valB) {
                return strcmp($a->jam_mulai, $b->jam_mulai);
            }
            return $valA <=> $valB;
        });

        $last = null;
        foreach ($sortedSelections as $p) {
            if ($last && $last->hari === $p->hari) {
                if ($p->jam_mulai < $last->jam_berakhir && $p->jam_berakhir > $last->jam_mulai) {
                    return back()->withErrors(['msg' => "Bentrok jadwal terdeteksi antara {$last->kode_mk} dan {$p->kode_mk} pada hari {$p->hari}."])->withInput();
                }
            }
            $last = $p;
        }

        // 3. Save Selections
        DB::beginTransaction();
        try {
            // Clear existing for this TA (Re-enrollment)
            Dkbs::where('nrp', $mahasiswa->nrp)
                ->where('tahun_ajaran', $activeTa)
                ->delete();

            foreach ($selectedPerkuliahan as $p) {
                Dkbs::create([
                    'nrp' => $mahasiswa->nrp,
                    'id_perkuliahan' => $p->id_perkuliahan,
                    'kode_mk' => $p->kode_mk,
                    'tahun_ajaran' => $activeTa,
                    'status' => 'Terdaftar',
                    'semester' => $p->mataKuliah->semester ?? $currentSemester
                ]);

                // Auto-enroll in paired class (Teori <-> Praktikum) if exists
                $this->autoEnrollPair($mahasiswa->nrp, $p, $activeTa, $selectedKodeMk);
            }

            DB::commit();
            return redirect('/mahasiswa/dkbs')->with('success', 'Pendaftaran kelas berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Gagal menyimpan pendaftaran: ' . $e->getMessage()])->withInput();
        }
    }

    private function autoEnrollPair($nrp, $perkuliahan, $activeTa, $selectedKodeMk)
    {
        $pairedKodeMk = null;
        $baseMk = substr($perkuliahan->kode_mk, 0, -1);
        $lastChar = substr($perkuliahan->kode_mk, -1);

        if ($lastChar === 'T') {
            $pairedKodeMk = $baseMk . 'P';
        } elseif ($lastChar === 'P') {
            $pairedKodeMk = $baseMk . 'T';
        }

        if ($pairedKodeMk && !in_array($pairedKodeMk, $selectedKodeMk)) {
            $pairedPerkuliahan = Perkuliahan::where('kode_mk', $pairedKodeMk)
                ->where('kelas', $perkuliahan->kelas)
                ->where('tahun_ajaran', $activeTa)
                ->first();

            if ($pairedPerkuliahan) {
                Dkbs::create([
                    'nrp' => $nrp,
                    'id_perkuliahan' => $pairedPerkuliahan->id_perkuliahan,
                    'kode_mk' => $pairedPerkuliahan->kode_mk,
                    'tahun_ajaran' => $activeTa,
                    'status' => 'Terdaftar',
                    'semester' => $pairedPerkuliahan->mataKuliah->semester
                ]);
            }
        }
    }
}
