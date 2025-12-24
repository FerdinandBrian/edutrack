@extends('layouts.admin')

@section('title','Dashboard Admin')

@section('content')

<!-- CARD GRID -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- MANAJEMEN USER -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-slate-800">Manajemen User</h3>
        <p class="text-sm text-slate-500 mt-1">Tambah, edit, atau hapus user</p>
        <a href="#" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Kelola user → 
        </a>
    </div>

    <!-- MANAJEMEN KELAS -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-slate-800">Manajemen Kelas</h3>
        <p class="text-sm text-slate-500 mt-1">Atur kelas dan dosen pengampu</p>
        <a href="#" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Kelola kelas → 
        </a>
    </div>

    <!-- LAPORAN -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold text-slate-800">Laporan Sistem</h3>
        <p class="text-sm text-slate-500 mt-1">Lihat rekapitulasi data akademik</p>
        <a href="#" class="text-blue-600 text-sm font-medium inline-block mt-4">
            Lihat laporan → 
        </a>
    </div>

</div>

@endsection
