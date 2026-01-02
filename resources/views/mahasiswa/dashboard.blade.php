@extends('layouts.mahasiswa')

@section('title','Dashboard Mahasiswa')

@section('content')

<!-- CARD GRID -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

    <!-- PENGUMUMAN -->
    <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-100 transition-colors">
            <span class="text-2xl">📢</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg">Pengumuman</h3>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">Informasi terbaru seputar kegiatan akademik dan kampus.</p>
        <div class="mt-6">
            <a href="/mahasiswa/pengumuman" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-amber-200">
                Lihat Pengumuman
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- MATA KULIAH -->
    <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-indigo-100 transition-colors">
            <span class="text-2xl">📚</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg">Mata Kuliah</h3>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">Daftar mata kuliah yang tersedia dan deskripsinya.</p>
        <div class="mt-6">
            <a href="/mahasiswa/mata-kuliah" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-indigo-200">
                Lihat Mata Kuliah
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- PRESENSI -->
    <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-100 transition-colors">
            <span class="text-2xl">⏱</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg">Presensi</h3>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">Pantau riwayat kehadiran dan status absensi kuliah.</p>
        <div class="mt-6">
            <a href="/mahasiswa/presensi" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-blue-200">
                Cek Kehadiran
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- DKBS -->
    <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-100 transition-colors">
            <span class="text-2xl">📘</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg">DKBS</h3>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">Daftar Kelas dan Beban Studi semester aktif.</p>
        <div class="mt-6">
            <a href="/mahasiswa/dkbs" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-emerald-200">
                Lihat DKBS
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- JADWAL -->
    <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-rose-100 transition-colors">
            <span class="text-2xl">📅</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg">Jadwal Kuliah</h3>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">Informasi waktu dan tempat pelaksanaan perkuliahan.</p>
        <div class="mt-6">
            <a href="/mahasiswa/jadwal" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-rose-200">
                Lihat Jadwal
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- NILAI -->
    <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="w-12 h-12 bg-cyan-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-cyan-100 transition-colors">
            <span class="text-2xl">📈</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg">Nilai</h3>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">Rekapitulasi hasil studi dan nilai per semester.</p>
        <div class="mt-6">
            <a href="/mahasiswa/nilai" class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-cyan-200">
                Lihat Nilai
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- PEMBAYARAN -->
    <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
        <div class="w-12 h-12 bg-violet-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-violet-100 transition-colors">
            <span class="text-2xl">💳</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg">Pembayaran</h3>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">Status tagihan dan riwayat pembayaran uang kuliah.</p>
        <div class="mt-6">
            <a href="/mahasiswa/pembayaran" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-violet-200">
                Cek Tagihan
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>

@endsection