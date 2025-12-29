<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function index()
    {
        try {
            if (auth()->user()->id_role == 3) {
                $col = \Illuminate\Support\Facades\Schema::hasColumn((new Tagihan)->getTable(), 'nrp') ? 'nrp' : 'npr';
                $data = Tagihan::with('mahasiswa')->where($col, auth()->user()->nrp)->get();
            } else {
                $data = Tagihan::with('mahasiswa')->get();
            }
        } catch (\Exception $e) {
            \Log::error('Tagihan index error: '. $e->getMessage());
            session()->flash('error', 'Terjadi masalah saat memuat pembayaran. Cek log.');
            $data = collect();
        }
        return view('pembayaran.index', ['data' => $data]);
    }

    public function create()
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        return view('pembayaran.create', compact('mahasiswas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nrp' => 'required|string',
            'jenis' => 'required|string',
            'jumlah' => 'required|numeric',
            'status' => 'nullable|string'
        ]);

        Tagihan::create($validated);
        return redirect('/pembayaran')->with('success','Tagihan dibuat');
    }

    public function show($id)
    {
        try {
            $row = Tagihan::with('mahasiswa')->findOrFail($id);
        } catch (\Exception $e) {
            \Log::error('Tagihan show error: '. $e->getMessage());
            return redirect('/pembayaran')->with('error','Data pembayaran tidak ditemukan atau terjadi masalah.');
        }
        return view('pembayaran.show', compact('row'));
    }

    public function edit(Tagihan $tagihan)
    {
        $mahasiswas = Mahasiswa::orderBy('nama')->get();
        return view('pembayaran.edit', compact('tagihan','mahasiswas'));
    }

    public function update(Request $request, Tagihan $tagihan)
    {
        $validated = $request->validate([
            'nrp' => 'required|string',
            'jenis' => 'required|string',
            'jumlah' => 'required|numeric',
            'status' => 'nullable|string'
        ]);

        $tagihan->update($validated);
        return redirect('/pembayaran')->with('success','Tagihan diperbarui');
    }

    public function destroy(Tagihan $tagihan)
    {
        $tagihan->delete();
        return back()->with('success','Tagihan dihapus');
    }
}
