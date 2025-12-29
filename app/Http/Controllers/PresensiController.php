<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Mahasiswa;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index()
    {
        // If mahasiswa, show only their records. Otherwise show all.
        if (auth()->user()->id_role == 3) {
            $col = \Illuminate\Support\Facades\Schema::hasColumn((new Presensi)->getTable(), 'nrp') ? 'nrp' : 'npr';
            $data = Presensi::with(['mahasiswa','jadwal'])->where($col, auth()->user()->nrp)->get();
        } else {
            $data = Presensi::with(['mahasiswa','jadwal'])->get();
        }

        return view('presensi.index', compact('data'));
    }

    public function create()
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        $jadwals = Jadwal::orderBy('hari')->get();
        return view('presensi.create', compact('mahasiswas','jadwals'));
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
        return redirect('/presensi')->with('success','Presensi tersimpan');
    }

    public function edit(Presensi $presensi)
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        $jadwals = Jadwal::orderBy('hari')->get();
        return view('presensi.edit', compact('presensi','mahasiswas','jadwals'));
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
        return redirect('/presensi')->with('success','Presensi diperbarui');
    }

    public function destroy(Presensi $presensi)
    {
        $presensi->delete();
        return back()->with('success','Presensi dihapus');
    }

    public function show(Presensi $presensi)
    {
        return view('presensi.show', compact('presensi'));
    }
}
