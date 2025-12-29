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
        } else {
            $data = $query->get();
        }

        $tahun_ajarans = Dkbs::distinct()->pluck('tahun_ajaran');

        return view($user->role . '.dkbs.index', compact('data', 'tahun_ajarans'));
    }

    public function create()
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        return view('admin.dkbs.create', compact('mahasiswas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nrp' => 'required',
            'id_perkuliahan' => 'required|exists:perkuliahan,id_perkuliahan',
            'status' => 'nullable|string'
        ]);

        $perkuliahan = Perkuliahan::findOrFail($request->id_perkuliahan);

        // Duplication Check
        $exists = Dkbs::where('nrp', $request->nrp)
            ->where('kode_mk', $perkuliahan->kode_mk)
            ->where('tahun_ajaran', $perkuliahan->tahun_ajaran)
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Mahasiswa sudah mengambil mata kuliah ini di periode tersebut.'])->withInput();
        }

        Dkbs::create([
            'nrp' => $request->nrp,
            'id_perkuliahan' => $request->id_perkuliahan,
            'kode_mk' => $perkuliahan->kode_mk,
            'tahun_ajaran' => $perkuliahan->tahun_ajaran,
            'status' => $request->status ?? 'Terdaftar',
            'semester' => $perkuliahan->mataKuliah->semester
        ]);
        
        return redirect('/admin/dkbs')->with('success','DKBS tersimpan');
    }

    public function edit(Dkbs $dkbs)
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        return view('admin.dkbs.edit', compact('dkbs','mahasiswas'));
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

    public function destroy(Dkbs $id)
    {
        $id->delete();
        return back()->with('success','DKBS dihapus');
    }

    public function getPerkuliahanByTahunAjaran(Request $request)
    {
        $ta = $request->tahun_ajaran;
        $data = Perkuliahan::with('mataKuliah')
            ->where('tahun_ajaran', $ta)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id_perkuliahan,
                    'label' => "[{$p->kode_mk}] {$p->mataKuliah->nama_mk} - Kelas {$p->kelas}"
                ];
            });
        return response()->json($data);
    }

    public function getMataKuliahBySemester($semester)
    {
        $courses = MataKuliah::where('semester', $semester)->get();
        return response()->json($courses);
    }
}
