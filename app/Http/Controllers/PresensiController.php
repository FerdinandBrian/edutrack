<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Mahasiswa;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PresensiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'mahasiswa') {
            $col = Schema::hasColumn((new Presensi)->getTable(), 'nrp') ? 'nrp' : 'npr';
            $data = Presensi::with(['mahasiswa','jadwal'])->where($col, $user->identifier)->get();
        } else {
            $data = Presensi::with(['mahasiswa','jadwal'])->get();
        }

        return view($user->role . '.presensi.index', compact('data'));
    }

    public function create()
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        $jadwals = Jadwal::orderBy('hari')->get();
        return view('dosen.presensi.create', compact('mahasiswas','jadwals'));
    }

    public function store(Request $request)
    {
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
        $jadwals = Jadwal::orderBy('hari')->get();
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

    public function show(Presensi $presensi)
    {
        $user = auth()->user();
        return view($user->role . '.presensi.show', compact('presensi'));
    }
}
