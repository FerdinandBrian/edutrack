@extends('layouts.mahasiswa')

@section('title', 'Dashboard Mahasiswa')

@section('content')

<!-- HEADER BANNER -->
<div class="relative overflow-hidden rounded-3xl bg-blue-600 px-8 py-12 text-white shadow-xl shadow-blue-200 mb-10">
    <div class="relative z-10">
        <h1 class="text-3xl font-bold tracking-tight mb-2">Selamat Datang, {{ auth()->user()->nama }}! 👋</h1>
        <p class="text-blue-100 text-lg opacity-90 max-w-xl">Akses semua informasi akademik dan perkuliahanmu dalam satu portal terintegrasi.</p>
        
        <div class="mt-8 flex gap-3">
            <a href="/mahasiswa/jadwal" class="px-5 py-2.5 bg-white text-blue-600 font-bold rounded-xl shadow-lg shadow-blue-900/10 hover:bg-blue-50 transition active:scale-95 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Cek Jadwal Hari Ini
            </a>
            <a href="/mahasiswa/profile" class="px-5 py-2.5 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-400 transition ring-1 ring-white/20">
                Edit Profil
            </a>
        </div>
    </div>
    
    <!-- Abstract Shapes -->
    <div class="absolute right-0 top-0 h-full w-1/2 pointer-events-none">
        <div class="absolute right-[-50px] top-[-50px] w-64 h-64 bg-blue-400/30 rounded-full blur-3xl"></div>
        <div class="absolute right-[100px] bottom-[-50px] w-48 h-48 bg-indigo-400/30 rounded-full blur-3xl"></div>
    </div>
</div>

<!-- QUICK STATS / INFO -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center font-bold text-xl">
            📝
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Status Akademik</p>
            <p class="text-lg font-bold text-slate-700">Aktif - 2024/2025</p>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center font-bold text-xl">
            🎓
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total SKS Diambil</p>
            <p class="text-lg font-bold text-slate-700">84 SKS</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center font-bold text-xl">
            ✨
        </div>
        <div>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">IPK Terakhir</p>
            <p class="text-lg font-bold text-slate-700">3.85</p>
        </div>
    </div>
</div>

<!-- MAIN MENU GRID -->
<div class="mb-6 flex items-center justify-between">
    <h3 class="font-bold text-slate-800 text-lg">Menu Akademik</h3>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- JADWAL KULIAH -->
    <a href="/mahasiswa/jadwal" class="group relative overflow-hidden bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-transform group-hover:scale-110">
            <svg class="w-24 h-24 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
        </div>
        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <h4 class="font-bold text-slate-800 text-xl mb-2">Jadwal Kuliah</h4>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">Cek jadwal pertemuan kelas, ujian, dan ruangan.</p>
        <span class="text-blue-600 font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
            Buka Jadwal <span class="text-lg">→</span>
        </span>
    </a>

    <!-- RENCANA STUDI -->
    <a href="/mahasiswa/dkbs" class="group relative overflow-hidden bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-transform group-hover:scale-110">
            <svg class="w-24 h-24 text-emerald-600" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
        </div>
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h4 class="font-bold text-slate-800 text-xl mb-2">DKBS / IRS</h4>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">Isi rencana studi semester baru dan cetak kartu ujian.</p>
        <span class="text-emerald-600 font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
            Isi Rencana Studi <span class="text-lg">→</span>
        </span>
    </a>

    <!-- HASIL STUDI -->
    <a href="/mahasiswa/nilai" class="group relative overflow-hidden bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-transform group-hover:scale-110">
            <svg class="w-24 h-24 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        </div>
        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-600 group-hover:text-white transition-colors shadow-sm">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <h4 class="font-bold text-slate-800 text-xl mb-2">Transkrip Nilai</h4>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">Lihat riwayat nilai per semester dan transkrip sementara.</p>
        <span class="text-amber-600 font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
            Lihat Transkrip <span class="text-lg">→</span>
        </span>
    </a>

    <!-- KEUANGAN -->
    <a href="/mahasiswa/pembayaran" class="group relative overflow-hidden bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-transform group-hover:scale-110">
            <svg class="w-24 h-24 text-violet-600" viewBox="0 0 24 24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
        </div>
        <div class="w-14 h-14 bg-violet-50 text-violet-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-violet-600 group-hover:text-white transition-colors shadow-sm">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <h4 class="font-bold text-slate-800 text-xl mb-2">Tagihan & UKT</h4>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">Status pembayaran biaya pendidikan dan riwayat transaksi.</p>
        <span class="text-violet-600 font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
            Cek Tagihan <span class="text-lg">→</span>
        </span>
    </a>

    <!-- PRESENSI -->
    <a href="/mahasiswa/presensi" class="group relative overflow-hidden bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-transform group-hover:scale-110">
            <svg class="w-24 h-24 text-rose-600" viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zM12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
        </div>
        <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-rose-600 group-hover:text-white transition-colors shadow-sm">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h4 class="font-bold text-slate-800 text-xl mb-2">Presensi Kelas</h4>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">Pantau kehadiranmu di setiap mata kuliah semester ini.</p>
        <span class="text-rose-600 font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
            Lihat Kehadiran <span class="text-lg">→</span>
        </span>
    </a>

    <!-- PENGUMUMAN -->
    <a href="/mahasiswa/pengumuman" class="group relative overflow-hidden bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-transform group-hover:scale-110">
            <svg class="w-24 h-24 text-cyan-600" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/></svg>
        </div>
        <div class="w-14 h-14 bg-cyan-50 text-cyan-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-cyan-600 group-hover:text-white transition-colors shadow-sm">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <h4 class="font-bold text-slate-800 text-xl mb-2">Pengumuman</h4>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">Info terbaru dan berita kampus yang relevan untukmu.</p>
        <span class="text-cyan-600 font-semibold text-sm flex items-center gap-2 group-hover:gap-3 transition-all">
            Baca Berita <span class="text-lg">→</span>
        </span>
    </a>

</div>

@endsection