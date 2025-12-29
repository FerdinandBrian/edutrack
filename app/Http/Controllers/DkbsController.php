<?php

namespace App\Http\Controllers;

use App\Models\Dkbs;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class DkbsController extends Controller
{
    public function index()
    {
        if (auth()->user()->id_role == 3) {
            $col = \Illuminate\Support\Facades\Schema::hasColumn((new Dkbs)->getTable(), 'nrp') ? 'nrp' : 'npr';
            $data = Dkbs::with('mahasiswa')->where($col, auth()->user()->nrp)->get();
        } else {
            $data = Dkbs::with('mahasiswa')->get();
        }
        return view('dkbs.index', compact('data'));
    }

    public function create()
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        return view('dkbs.create', compact('mahasiswas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nrp' => 'required|string',
            'kode_mk' => 'required|string',
            'semester' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        Dkbs::create($validated);
        return redirect('/dkbs')->with('success','DKBS tersimpan');
    }

    public function edit(Dkbs $dkbs)
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        return view('dkbs.edit', compact('dkbs','mahasiswas'));
    }

    public function update(Request $request, Dkbs $dkbs)
    {
        $validated = $request->validate([
            'nrp' => 'required|string',
            'kode_mk' => 'required|string',
            'semester' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $dkbs->update($validated);
        return redirect('/dkbs')->with('success','DKBS diperbarui');
    }

    public function destroy(Dkbs $dkbs)
    {
        $dkbs->delete();
        return back()->with('success','DKBS dihapus');
    }
}
