<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Simple Calendar Logic
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        
        // Fetch announcements for this month
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $events = Pengumuman::where(function($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('waktu_mulai', [$startOfMonth, $endOfMonth])
              ->orWhereBetween('waktu_selesai', [$startOfMonth, $endOfMonth]);
        })->get();
        
        // Fetch all recent announcements for list view
        $list = Pengumuman::orderBy('waktu_mulai', 'asc')->get();

        return view('pengumuman.index', compact('events', 'list', 'month', 'year'));
    }

    public function create()
    {
        return view('admin.pengumuman.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
            'kategori' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
        ]);

        Pengumuman::create($validated);

        return redirect('/admin/pengumuman')->with('success', 'Pengumuman berhasil dibuat');
    }

    public function edit(Pengumuman $pengumuman)
    {
        return view('admin.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
            'kategori' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
        ]);

        $pengumuman->update($validated);

        return redirect('/admin/pengumuman')->with('success', 'Pengumuman berhasil diperbarui');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();
        return back()->with('success', 'Pengumuman dihapus');
    }
}
