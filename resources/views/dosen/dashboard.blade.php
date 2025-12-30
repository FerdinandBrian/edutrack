@extends('layouts.dosen')

@section('title','Dashboard Dosen')

@section('content')

<!-- CARD GRID -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- PRESENSI MAHASISWA -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-slate-800">Presensi</h3>
        <p class="text-sm text-slate-500 mt-1">Lihat daftar kehadiran mahasiswa</p>
        <a href="/dosen/presensi" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Lihat presensi → 
        </a>
        <a href="/dosen/nilai" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Lihat semua nilai → 
        </a>
        <a href="/dosen/jadwal" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Lihat jadwal → 
        </a>
    </div>

    <!-- NILAI MAHASISWA -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-slate-800">Nilai Mahasiswa</h3>
        <p class="text-sm text-slate-500 mt-1">Rekap nilai mahasiswa</p>
        <a href="/dosen/nilai" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Lihat nilai → 
        </a>
    </div>

    <!-- JADWAL MENGAJAR -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-slate-800">Jadwal Mengajar</h3>
        <p class="text-sm text-slate-500 mt-1">Jadwal perkuliahan yang diampu</p>
        <a href="/dosen/jadwal" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Lihat jadwal → 
        </a>
    </div>

</div>

@endsection
