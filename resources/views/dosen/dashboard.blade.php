@extends('layouts.dosen')

@section('title','Dashboard Dosen')

@section('content')

<!-- CARD GRID -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">

    <!-- PRESENSI MAHASISWA -->
    <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-start justify-between mb-4">
            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                <span class="text-3xl group-hover:scale-110 transition-transform">⏱</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Kehadiran</span>
            </div>
        </div>
        <h3 class="font-bold text-slate-800 text-xl">Presensi Mahasiswa</h3>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">Pantau daftar hadir mahasiswa di setiap sesi perkuliahan yang Anda ampu.</p>
        <div class="mt-6">
            <a href="/dosen/presensi" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm shadow-blue-200">
                Buka Presensi
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
    </div>

    <!-- NILAI MAHASISWA -->
    <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-start justify-between mb-4">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all">
                <span class="text-3xl group-hover:scale-110 transition-transform">📈</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Akademik</span>
            </div>
        </div>
        <h3 class="font-bold text-slate-800 text-xl">Input Nilai</h3>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">Berikan penilaian tugas, kuis, UTS, dan UAS untuk mahasiswa Anda.</p>
        <div class="mt-6">
            <a href="/dosen/nilai" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm shadow-emerald-200">
                Kelola Nilai
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
    </div>

    <!-- JADWAL MENGAJAR -->
    <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-start justify-between mb-4">
            <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all">
                <span class="text-3xl group-hover:scale-110 transition-transform">📅</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Agenda</span>
            </div>
        </div>
        <h3 class="font-bold text-slate-800 text-xl">Jadwal Mengajar</h3>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">Lihat agenda harian dan mingguan ruang kelas serta jam mengajar aktif.</p>
        <div class="mt-6">
            <a href="/dosen/jadwal" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm shadow-indigo-200">
                Lihat Jadwal
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
    </div>

    <!-- PENGUMUMAN -->
    <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-start justify-between mb-4">
            <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all">
                <span class="text-3xl group-hover:scale-110 transition-transform">📢</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Informasi</span>
            </div>
        </div>
        <h3 class="font-bold text-slate-800 text-xl">Pengumuman</h3>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">Pantau informasi terbaru seputar kampus dan agenda akademik resmi.</p>
        <div class="mt-6">
            <a href="/dosen/pengumuman" class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition-all shadow-sm shadow-amber-200">
                Lihat Berita
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
    </div>

</div>

@endsection
