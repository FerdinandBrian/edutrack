<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index()
    {
        $data = Presensi::with('mahasiswa')->get();
        return view('presensi.index', compact('data'));
    }

    public function store(Request $request)
    {
        Presensi::create($request->all());
        return back()->with('success','Presensi tersimpan');
    }
}
