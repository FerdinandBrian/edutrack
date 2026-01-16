<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'mahasiswa') {
            // Ambil data jadwal dari DKBS mahasiswa yang login
            $nrp = $user->identifier;
            
            // Get available semesters
            $tahun_ajarans = \App\Models\Dkbs::where('nrp', $nrp)
                ->distinct()
                ->orderBy('tahun_ajaran', 'desc')
                ->pluck('tahun_ajaran');
            
            $selectedTa = $request->query('tahun_ajaran') ?? $tahun_ajarans->first();

            $data = \App\Models\Dkbs::with(['perkuliahan.mataKuliah', 'perkuliahan.dosen', 'perkuliahan.ruangan'])
                ->where('nrp', $nrp)
                ->when($selectedTa, function($q) use ($selectedTa) {
                    $q->where('tahun_ajaran', $selectedTa);
                })
                ->get();

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
            // For Dosen
            if ($user->role === 'dosen') {
                $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
                if ($dosen) {
                    $data = \App\Models\Perkuliahan::with(['mataKuliah', 'ruangan'])
                        ->where('nip_dosen', $dosen->nip)
                        ->get();
                } else {
                    $data = collect();
                }
            } else {
                // For Admin
                $data = \App\Models\Perkuliahan::with(['mataKuliah', 'dosen', 'ruangan'])->get();
            }

            // Custom sorting for Days
            $dayOrder = [
                'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7
            ];

             $data = $data->sort(function($a, $b) use ($dayOrder) {
                // sort by day
                $dayA = $a->hari ?? '';
                $dayB = $b->hari ?? '';
                $valA = $dayOrder[$dayA] ?? 99;
                $valB = $dayOrder[$dayB] ?? 99;
                
                if ($valA === $valB) {
                    // if day same, sort by time
                    return strcmp($a->jam_mulai, $b->jam_mulai);
                }
            });
            
            $tahun_ajarans = collect();
            $selectedTa = null;
        }

        return view($user->role . '.jadwal.index', compact('data', 'tahun_ajarans', 'selectedTa'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->role === 'dosen') {
            $dosen = Dosen::where('user_id', $user->id)->first();
            $taughtMkCodes = \App\Models\Perkuliahan::where('nip_dosen', $dosen->nip)->pluck('kode_mk')->unique();
            $mataKuliahs = \App\Models\MataKuliah::whereIn('kode_mk', $taughtMkCodes)->get();
            return view('dosen.jadwal.create', compact('mataKuliahs'));
        }
        
        $dosens = Dosen::orderBy('nama')->get();
        return view('dosen.jadwal.create', compact('dosens')); // Support Admin fallback
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role === 'dosen') {
            $dosen = Dosen::where('user_id', $user->id)->first();
            
            $validated = $request->validate([
                'kode_mk' => 'required|string',
                'hari' => 'required|string',
                'jam_mulai' => 'required',
                'jam_berakhir' => 'required',
            ]);

            // Save to Official Schedule (Perkuliahan)
            \App\Models\Perkuliahan::create([
                'kode_mk' => $validated['kode_mk'],
                'nip_dosen' => $dosen->nip,
                'hari' => $validated['hari'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_berakhir' => $validated['jam_berakhir'],
                'kode_ruangan' => 'L8001', // Default
                'kelas' => 'A', // Default or need input
                'tahun_ajaran' => '2025/2026 - Ganjil',
            ]);

            return redirect('/dosen/jadwal')->with('success','Jadwal berhasil ditambahkan');
        }

        // Fallback for old functionality if needed
        $validated = $request->validate([
             'kode_mk' => 'required',
             'hari' => 'required',
             'jam' => 'required'
        ]);
        Jadwal::create($validated);
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
