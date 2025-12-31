@extends('layouts.mahasiswa')

@section('title','Dashboard Mahasiswa')

@section('content')

<!-- CARD GRID -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- PRESENSI -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-slate-800">Presensi</h3>
        <p class="text-sm text-slate-500 mt-1">Cek kehadiran perkuliahan</p>
        <a href="/mahasiswa/presensi" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Lihat presensi →
        </a>
    </div>

    <!-- DKBS -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-slate-800">DKBS</h3>
        <p class="text-sm text-slate-500 mt-1">Daftar kelas & beban studi</p>
        <a href="/mahasiswa/dkbs" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Lihat DKBS →
        </a>
    </div>

    <!-- JADWAL -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-slate-800">Jadwal Kuliah</h3>
        <p class="text-sm text-slate-500 mt-1">Jadwal perkuliahan aktif</p>
        <a href="/mahasiswa/jadwal" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Lihat jadwal →
        </a>
    </div>

    <!-- NILAI -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-slate-800">Nilai</h3>
        <p class="text-sm text-slate-500 mt-1">Rekap nilai per semester</p>
        <a href="/mahasiswa/nilai" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Lihat nilai →
        </a>
    </div>

    <!-- PEMBAYARAN -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-slate-800">Pembayaran</h3>
        <p class="text-sm text-slate-500 mt-1">Status pembayaran kuliah</p>
        <a href="/mahasiswa/pembayaran" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Lihat pembayaran →
        </a>
    </div>
</div>

@endsection