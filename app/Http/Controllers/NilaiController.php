<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Mahasiswa;
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
                $col = Schema::hasColumn((new Nilai)->getTable(), 'nrp') ? 'nrp' : 'npr';
                $data = Nilai::with('mahasiswa')->where($col, $user->identifier)->get();
            } else {
                $data = Nilai::with('mahasiswa')->get();
            }
        } catch (\Exception $e) {
            Log::error('Nilai index error: '. $e->getMessage());
            session()->flash('error', 'Terjadi masalah saat memuat nilai. Cek log.');
            $data = collect();
        }

        return view($user->role . '.nilai.index', compact('data'));
    }

    public function create()
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        return view('dosen.nilai.create', compact('mahasiswas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nrp' => 'required|string',
            'kode_mk' => 'required|string',
            'nilai' => 'required|numeric',
        ]);

        Nilai::create($validated);
        
        $user = auth()->user();
        return redirect('/' . $user->role . '/nilai')->with('success','Nilai tersimpan');
    }

    public function show($id)
    {
        $user = auth()->user();
        try {
            $row = Nilai::with('mahasiswa')->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Nilai show error: '. $e->getMessage());
            return redirect('/' . $user->role . '/nilai')->with('error','Data nilai tidak ditemukan atau terjadi masalah.');
        }

        return view($user->role . '.nilai.show', compact('row'));
    }

    public function edit(Nilai $nilai)
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        return view('dosen.nilai.edit', compact('nilai','mahasiswas'));
    }

    public function update(Request $request, Nilai $nilai)
    {
        $validated = $request->validate([
            'nrp' => 'required|string',
            'kode_mk' => 'required|string',
            'nilai' => 'required|numeric',
        ]);

        $nilai->update($validated);
        
        $user = auth()->user();
        return redirect('/' . $user->role . '/nilai')->with('success','Nilai diperbarui');
    }

    public function destroy(Nilai $nilai)
    {
        $nilai->delete();
        return back()->with('success','Nilai dihapus');
    }
}
