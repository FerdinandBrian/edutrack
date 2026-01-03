<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Mahasiswa;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Dkbs;

class PresensiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'dosen') {
            // Group By Session (Jadwal + Tanggal)
            $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
            
            if ($dosen) {
                // Get IDs of classes taught by this dosen
                $myJadwalIds = \App\Models\Perkuliahan::where('nip_dosen', $dosen->nip)->pluck('id_perkuliahan');

                $sessions = Presensi::with(['jadwal.mataKuliah', 'jadwal.ruangan'])
                    ->whereIn('jadwal_id', $myJadwalIds)
                    ->select('jadwal_id', 'tanggal', \Illuminate\Support\Facades\DB::raw('count(*) as total_students'))
                    ->groupBy('jadwal_id', 'tanggal')
                    ->orderBy('tanggal', 'desc')
                    ->get();
                    
            } else {
                $sessions = collect();
            }
            
            return view('dosen.presensi.index', compact('sessions'));

        } elseif ($user->role === 'mahasiswa') {
            // Summary per course for Mahasiswa (Image 1)
            $nrp = $user->identifier;
            
            // Get enrolled courses via DKBS
            $enrolledCourses = \App\Models\Dkbs::with(['mataKuliah', 'perkuliahan'])
                ->where('nrp', $nrp)
                ->get();
            
            $summary = $enrolledCourses->map(function($dkbs) use ($nrp) {
                // Count attendance for this specific perkuliahan/course session
                $attendance = Presensi::where('nrp', $nrp)
                    ->where('jadwal_id', $dkbs->id_perkuliahan)
                    ->get();
                
                $hadir = $attendance->where('status', 'Hadir')->count();
                $alpha = $attendance->where('status', 'Absen')->count();
                $izin = $attendance->where('status', 'Izin')->count();
                $totalPertemuan = $attendance->count();
                
                $persentase = $totalPertemuan > 0 
                    ? round(($hadir / $totalPertemuan) * 100, 2) 
                    : 0;

                return (object)[
                    'jadwal_id' => $dkbs->id_perkuliahan,
                    'kode_mk' => $dkbs->mataKuliah->kode_mk ?? '-',
                    'nama_mk' => $dkbs->mataKuliah->nama_mk ?? '-',
                    'sks' => $dkbs->mataKuliah->sks ?? 0,
                    'alpha' => $alpha,
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'total_pertemuan' => $totalPertemuan,
                    'persentase' => $persentase
                ];
            });

            return view('mahasiswa.presensi.index', compact('summary'));
        } else {
            // Admin View
             $sessions = Presensi::with(['jadwal.mataKuliah'])
                    ->select('jadwal_id', 'tanggal', \Illuminate\Support\Facades\DB::raw('count(*) as total_students'))
                    ->groupBy('jadwal_id', 'tanggal')
                    ->orderBy('tanggal', 'desc')
                    ->get();
            return view('dosen.presensi.index', compact('sessions'));
        }
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        
        // 1. Fetch Schedules (Jadwal/Perkuliahan)
        if ($user->role === 'dosen') {
             $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
             if ($dosen) {
                 $jadwals = \App\Models\Perkuliahan::with('mataKuliah')
                            ->where('nip_dosen', $dosen->nip)
                            ->orderBy('hari')
                            ->get();
             } else {
                 $jadwals = collect();
             }
        } else {
             $jadwals = \App\Models\Perkuliahan::with('mataKuliah')->orderBy('hari')->get();
        }

        // 2. If Jadwal is selected, Fetch Students
        $mahasiswas = collect();
        $selectedJadwal = null;
        
        if ($request->filled('jadwal_id')) {
            $selectedJadwal = \App\Models\Perkuliahan::find($request->jadwal_id);
            if ($selectedJadwal) {
                 // Get students enrolled in this class via DKBS
                 $mahasiswas = Dkbs::with('mahasiswa')
                     ->where('id_perkuliahan', $request->jadwal_id)
                     ->get()
                     ->pluck('mahasiswa') // Extract Mahasiswa models
                     ->filter() // Remove nulls
                     ->sortBy('nama');
            }
        } else {
            // Keep fallback for manual single entry if needed, or just default to empty
            $mahasiswas = Mahasiswa::orderBy('nama')->get(); 
        }

        return view('dosen.presensi.create', compact('mahasiswas','jadwals', 'selectedJadwal'));
    }

    public function store(Request $request)
    {
        // Handle Bulk Store
        if ($request->has('bulk_presensi')) {
            $request->validate([
                'jadwal_id' => 'required',
                'tanggal' => 'required|date',
                'presensi' => 'required|array',
            ]);

            $jadwalId = $request->jadwal_id;
            $tanggal = $request->tanggal;
            $presensiData = $request->presensi; // Array of [nrp => [status => ..., keterangan => ...]]

            foreach ($presensiData as $nrp => $data) {
                Presensi::updateOrCreate(
                    [
                        'nrp' => $nrp,
                        'jadwal_id' => $jadwalId,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                    ]
                );
            }
            
            $user = auth()->user();
            return redirect('/' . $user->role . '/presensi')->with('success','Data presensi berhasil disimpan.');
        }

        // Fallback Single Store
        $validated = $request->validate([
            'nrp' => 'required|string',
            'jadwal_id' => 'nullable|integer',
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Absen,Izin',
            'keterangan' => 'nullable|string',
        ]);

        Presensi::create($validated);
        
        $user = auth()->user();
        return redirect('/' . $user->role . '/presensi')->with('success','Presensi tersimpan');
    }

    public function edit(Presensi $presensi)
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        
        $user = auth()->user();
        if ($user->role === 'dosen') {
             $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
             if ($dosen) {
                 $jadwals = \App\Models\Perkuliahan::with('mataKuliah')
                            ->where('nip_dosen', $dosen->nip)
                            ->orderBy('hari')
                            ->get();
             } else {
                 $jadwals = collect();
             }
        } else {
             $jadwals = \App\Models\Perkuliahan::with('mataKuliah')->orderBy('hari')->get();
        }

        return view('dosen.presensi.edit', compact('presensi','mahasiswas','jadwals'));
    }

    public function update(Request $request, Presensi $presensi)
    {
        $validated = $request->validate([
            'nrp' => 'required|string',
            'jadwal_id' => 'nullable|integer',
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Absen,Izin',
            'keterangan' => 'nullable|string',
        ]);

        $presensi->update($validated);
        
        $user = auth()->user();
        return redirect('/' . $user->role . '/presensi')->with('success','Presensi diperbarui');
    }

    public function destroy(Presensi $presensi)
    {
        $presensi->delete();
        return back()->with('success','Presensi dihapus');
    }

    public function show($id)
    {
        $user = auth()->user();
        
        if ($user->role === 'mahasiswa') {
            // Detailed attendance for a specific course (Image 2)
            $nrp = $user->identifier;
            $jadwalId = $id;
            
            $perkuliahan = \App\Models\Perkuliahan::with('mataKuliah')->find($jadwalId);
            $data = Presensi::with(['mahasiswa'])
                ->where('nrp', $nrp)
                ->where('jadwal_id', $jadwalId)
                ->orderBy('tanggal', 'desc')
                ->get();
                
            return view('mahasiswa.presensi.show', compact('data', 'perkuliahan'));
        }

        // Default behavior for other roles
        $presensi = Presensi::findOrFail($id);
        return view($user->role . '.presensi.show', compact('presensi'));
    }
}
