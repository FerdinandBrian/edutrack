@extends('layouts.dosen')

@section('title', 'Dashboard Dosen')

@section('content')

<!-- HEADER BANNER -->
<div class="relative overflow-hidden rounded-3xl bg-emerald-600 px-8 py-12 text-white shadow-xl shadow-emerald-200 mb-10">
    <div class="relative z-10">
        <h1 class="text-3xl font-bold tracking-tight mb-2">Selamat Datang, {{ (auth()->user()->dosen->jenis_kelamin ?? '') == 'Laki-laki' ? 'Bapak' : 'Ibu' }} {{ auth()->user()->nama }}! 👋</h1>
        <p class="text-emerald-100 text-lg opacity-90 max-w-xl">Kelola aktivitas mengajar, penilaian, dan presensi mahasiswa Anda dengan mudah.</p>
        
        <div class="mt-8 flex gap-3">
            <a href="/dosen/jadwal" class="px-5 py-2.5 bg-white text-emerald-600 font-bold rounded-xl shadow-lg shadow-emerald-900/10 hover:bg-emerald-50 transition active:scale-95 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Jadwal Mengajar Hari Ini
            </a>
            <a href="/dosen/profile" class="px-5 py-2.5 bg-emerald-500 text-white font-medium rounded-xl hover:bg-emerald-400 transition ring-1 ring-white/20">
                Edit Profil
            </a>
        </div>
    </div>
    
    <!-- Abstract Shapes -->
    <div class="absolute right-0 top-0 h-full w-1/2 pointer-events-none">
        <div class="absolute right-[-50px] top-[-50px] w-64 h-64 bg-emerald-400/30 rounded-full blur-3xl"></div>
        <div class="absolute right-[100px] bottom-[-50px] w-48 h-48 bg-teal-400/30 rounded-full blur-3xl"></div>
    </div>
</div>

<!-- QUICK STATS / INFO -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center font-bold text-xl">
            👨‍🏫
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Status Dosen</p>
            <p class="text-lg font-bold text-slate-700">Aktif - {{ $activeYear }}</p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center font-bold text-xl">
            📚
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Kelas Diampu</p>
            <p class="text-lg font-bold text-slate-700">{{ $totalKelas }} Kelas</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center font-bold text-xl">
            📝
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Mahasiswa</p>
            <p class="text-lg font-bold text-slate-700">{{ $totalMahasiswa }} Mahasiswa</p>
        </div>
    </div>
</div>

<!-- MAIN MENU GRID -->
<div class="mb-6 flex items-center justify-between">
    <h3 class="font-bold text-slate-800 text-lg">Menu Akademik</h3>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- JADWAL MENGAJAR -->
    <a href="/dosen/jadwal" class="group relative overflow-hidden bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-transform group-hover:scale-110">
            <svg class="w-24 h-24 text-emerald-600" viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
        </div>
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <h4 class="font-bold text-slate-800 text-xl mb-2">Jadwal Mengajar</h4>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">Cek jadwal kelas, ruangan, dan waktu mengajar Anda.</p>
        <span class="text-emerald-600 font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
            Lihat Jadwal <span class="text-lg">→</span>
        </span>
    </a>

    <!-- PRESENSI MAHASISWA -->
    <a href="/dosen/presensi" class="group relative overflow-hidden bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-transform group-hover:scale-110">
            <svg class="w-24 h-24 text-teal-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-1.07 3.97-2.9 5.4z"/></svg>
        </div>
        <div class="w-14 h-14 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-teal-600 group-hover:text-white transition-colors shadow-sm">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h4 class="font-bold text-slate-800 text-xl mb-2">Presensi Mahasiswa</h4>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">Kelola kehadiran mahasiswa di setiap pertemuan kelas.</p>
        <span class="text-teal-600 font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
            Buka Presensi <span class="text-lg">→</span>
        </span>
    </a>

    <!-- INPUT NILAI -->
    <a href="/dosen/nilai" class="group relative overflow-hidden bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-transform group-hover:scale-110">
            <svg class="w-24 h-24 text-sky-600" viewBox="0 0 24 24" fill="currentColor"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-6-9-6z"/></svg>
        </div>
        <div class="w-14 h-14 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-sky-600 group-hover:text-white transition-colors shadow-sm">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <h4 class="font-bold text-slate-800 text-xl mb-2">Input Nilai</h4>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">Masukkan nilai tugas, UTS, dan UAS mahasiswa.</p>
        <span class="text-sky-600 font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
            Kelola Nilai <span class="text-lg">→</span>
        </span>
    </a>

    <!-- PENGUMUMAN -->
    <a href="/dosen/pengumuman" class="group relative overflow-hidden bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-transform group-hover:scale-110">
            <svg class="w-24 h-24 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-600 group-hover:text-white transition-colors shadow-sm">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <h4 class="font-bold text-slate-800 text-xl mb-2">Pengumuman</h4>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">Info terbaru dan berita kampus untuk dosen.</p>
        <span class="text-amber-600 font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
            Baca Berita <span class="text-lg">→</span>
        </span>
    </a>

</div>

@endsection
