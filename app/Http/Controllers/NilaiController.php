<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Perkuliahan;
use App\Models\Dkbs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class NilaiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        try {
            if ($user->role === 'mahasiswa') {
                // Fetch DKBS (Enrolled Courses)
                $dkbs = Dkbs::with(['mataKuliah'])->where('nrp', $user->identifier)->get();
                
                // Fetch existing Grades
                $nilaiRecords = Nilai::where('nrp', $user->identifier)->get()->keyBy('kode_mk');

                // Merge Data
                $data = $dkbs->map(function($item) use ($nilaiRecords) {
                    $nilai = $nilaiRecords->get($item->kode_mk);
                    $item->nilai = $nilai; // Attach nilai object to dkbs item
                    return $item;
                });

            } elseif ($user->role === 'dosen') {
                $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
                if ($dosen) {
                     // Fetch Classes (Perkuliahan) taught by this lecturer
                     $classes = \App\Models\Perkuliahan::with(['mataKuliah', 'ruangan'])
                        ->where('nip_dosen', $dosen->nip)
                        ->orderBy('hari')
                        ->get();
                        
                    return view('dosen.nilai.index', compact('classes'));
                } else {
                    $classes = collect();
                    return view('dosen.nilai.index', compact('classes'));
                }
            } else {
                $data = Nilai::with(['mahasiswa', 'mataKuliah'])->get();
            }
        } catch (\Exception $e) {
            Log::error('Nilai index error: '. $e->getMessage());
            $data = collect();
        }

        return view($user->role . '.nilai.index', compact('data'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'dosen') {
            $dosen = Dosen::where('user_id', $user->id)->first();
            
            if ($dosen) {
                // Get Courses taught by this Dosen
                $perkuliahanQuery = Perkuliahan::where('nip_dosen', $dosen->nip);
                
                $assignedMkCodes = $perkuliahanQuery->pluck('kode_mk')->unique();
                $mataKuliahs = MataKuliah::whereIn('kode_mk', $assignedMkCodes)->get();

                // Get Students enrolled in these classes
                $perkuliahanIds = $perkuliahanQuery->pluck('id_perkuliahan');
                $nrps = Dkbs::whereIn('id_perkuliahan', $perkuliahanIds)->pluck('nrp')->unique();
                $mahasiswas = Mahasiswa::whereIn('nrp', $nrps)->orderBy('nama')->get();
            } else {
                $mataKuliahs = collect();
                $mahasiswas = collect();
            }
        } else {
            // Admin or others see all
            $mahasiswas = Mahasiswa::orderBy('nama')->get();
            $mataKuliahs = MataKuliah::all();
        }

        return view('dosen.nilai.create', compact('mahasiswas', 'mataKuliahs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nrp' => 'required|string',
            'kode_mk' => 'required|string',
            'p1' => 'nullable|numeric|min:0|max:100',
            'p2' => 'nullable|numeric|min:0|max:100',
            'p3' => 'nullable|numeric|min:0|max:100',
            'p4' => 'nullable|numeric|min:0|max:100',
            'p5' => 'nullable|numeric|min:0|max:100',
            'p6' => 'nullable|numeric|min:0|max:100',
            'p7' => 'nullable|numeric|min:0|max:100',
            'uts' => 'nullable|numeric|min:0|max:100',
            'p9' => 'nullable|numeric|min:0|max:100',
            'p10' => 'nullable|numeric|min:0|max:100',
            'p11' => 'nullable|numeric|min:0|max:100',
            'p12' => 'nullable|numeric|min:0|max:100',
            'p13' => 'nullable|numeric|min:0|max:100',
            'p14' => 'nullable|numeric|min:0|max:100',
            'p15' => 'nullable|numeric|min:0|max:100',
            'uas' => 'nullable|numeric|min:0|max:100',
        ]);

        // Calculate Total & Final Grade
        $scores = $request->only(['p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7', 'p9', 'p10', 'p11', 'p12', 'p13', 'p14', 'p15']);
        $kat_avg = collect($scores)->avg() ?: 0;
        
        $uts = $request->uts ?: 0;
        $uas = $request->uas ?: 0;

        // NEW WEIGHTS: Tasks 60%, UTS 20%, UAS 20%
        $total = ($kat_avg * 0.6) + ($uts * 0.2) + ($uas * 0.2);
        
        $data = $request->all();

        // Sanitize inputs: Convert nulls to 0
        foreach(['p1','p2','p3','p4','p5','p6','p7','uts','p9','p10','p11','p12','p13','p14','p15','uas'] as $field) {
            $data[$field] = $data[$field] ?? 0;
        }

        $data['nilai_total'] = round($total, 2);
        $data['nilai_akhir'] = Nilai::calculateGrade($total);

        Nilai::create($data);
        
        return redirect('/dosen/nilai')->with('success','Nilai berhasil disimpan');
    }

    public function edit($id)
    {
        $nilai = Nilai::findOrFail($id);
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        $mataKuliahs = MataKuliah::all();
        return view('dosen.nilai.edit', compact('nilai','mahasiswas', 'mataKuliahs'));
    }

    public function update(Request $request, $id)
    {
        $nilai = Nilai::findOrFail($id);
        
        $validated = $request->validate([
            'nrp' => 'required|string',
            'kode_mk' => 'required|string',
            'p1' => 'nullable|numeric|min:0|max:100',
            'p2' => 'nullable|numeric|min:0|max:100',
            'p3' => 'nullable|numeric|min:0|max:100',
            'p4' => 'nullable|numeric|min:0|max:100',
            'p5' => 'nullable|numeric|min:0|max:100',
            'p6' => 'nullable|numeric|min:0|max:100',
            'p7' => 'nullable|numeric|min:0|max:100',
            'uts' => 'nullable|numeric|min:0|max:100',
            'p9' => 'nullable|numeric|min:0|max:100',
            'p10' => 'nullable|numeric|min:0|max:100',
            'p11' => 'nullable|numeric|min:0|max:100',
            'p12' => 'nullable|numeric|min:0|max:100',
            'p13' => 'nullable|numeric|min:0|max:100',
            'p14' => 'nullable|numeric|min:0|max:100',
            'p15' => 'nullable|numeric|min:0|max:100',
            'uas' => 'nullable|numeric|min:0|max:100',
        ]);

        $scores = $request->only(['p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7', 'p9', 'p10', 'p11', 'p12', 'p13', 'p14', 'p15']);
        $kat_avg = collect($scores)->avg() ?: 0;
        
        $uts = $request->uts ?: 0;
        $uas = $request->uas ?: 0;

        // NEW WEIGHTS: Tasks 60%, UTS 20%, UAS 20%
        $total = ($kat_avg * 0.6) + ($uts * 0.2) + ($uas * 0.2);
        
        $data = $request->all();

        // Sanitize inputs: Convert nulls to 0
        foreach(['p1','p2','p3','p4','p5','p6','p7','uts','p9','p10','p11','p12','p13','p14','p15','uas'] as $field) {
            $data[$field] = $data[$field] ?? 0;
        }

        $data['nilai_total'] = round($total, 2);
        $data['nilai_akhir'] = Nilai::calculateGrade($total);

        $nilai->update($data);
        
        return redirect('/dosen/nilai')->with('success','Nilai berhasil diperbarui');
    }

    public function destroy($id)
    {
        Nilai::destroy($id);
        return back()->with('success','Nilai dihapus');
    }
    public function showClass($id)
    {
        $jadwal = \App\Models\Perkuliahan::with('mataKuliah')->findOrFail($id);
        
        // Get students in this class
        $students = Dkbs::with('mahasiswa')
            ->where('id_perkuliahan', $id)
            ->get()
            ->sortBy(function($dkbs) {
                return optional($dkbs->mahasiswa)->nama;
            });

        // Get grades for these students for this subject
        $grades = Nilai::where('kode_mk', $jadwal->kode_mk)
            ->whereIn('nrp', $students->pluck('nrp'))
            ->get()
            ->keyBy('nrp');

        return view('dosen.nilai.show_class', compact('jadwal', 'students', 'grades'));
    }
}
