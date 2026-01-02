@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 px-8 py-10 text-white shadow-2xl">
        <div class="relative z-10 max-w-2xl">
            <h1 class="text-3xl font-bold tracking-tight md:text-4xl">Selamat Datang, <span class="text-blue-400 capitalize">{{ Auth::user()->nama }}!</span></h1>
            <p class="mt-4 text-slate-300">Pusat kontrol administrasi Edutrack. Pantau dan kelola seluruh ekosistem akademik secara efisien.</p>
        </div>
        <!-- Decorative background elements -->
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="absolute -bottom-20 right-40 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pengguna</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ \App\Models\User::count() }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mata Kuliah</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ \App\Models\MataKuliah::count() }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kelas Aktif</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ \App\Models\Perkuliahan::count() }}</h3>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total DKBS</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ \App\Models\Dkbs::count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Section Header -->
    <div class="flex items-center gap-4 px-2">
        <h2 class="text-xl font-bold text-slate-800 whitespace-nowrap">Manajemen Sistem</h2>
        <div class="h-px bg-slate-200 w-full"></div>
    </div>

    <!-- Action Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- USER MANAGEMENT -->
        <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-all">
                <span class="text-2xl group-hover:scale-110 transition-transform">👥</span>
            </div>
            <h3 class="font-bold text-slate-800 text-lg">Manajemen User</h3>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Kelola data Mahasiswa, Dosen, dan Admin dalam sistem.</p>
            <div class="mt-6 flex gap-2">
                <a href="/admin/users" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-blue-200">
                    Kelola
                </a>
                <a href="/admin/users/create" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-bold rounded-lg transition-colors">
                    +
                </a>
            </div>
        </div>

        <!-- MATA KULIAH MASTER -->
        <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                <span class="text-2xl group-hover:scale-110 transition-transform">📚</span>
            </div>
            <h3 class="font-bold text-slate-800 text-lg">Master Mata Kuliah</h3>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Atur kurikulum, data mata kuliah, dan bobot SKS.</p>
            <div class="mt-6 flex gap-2">
                <a href="/admin/mata-kuliah" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-indigo-200">
                    Master Data
                </a>
                <a href="/admin/mata-kuliah/create" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-bold rounded-lg transition-colors">
                    +
                </a>
            </div>
        </div>

        <!-- JADWAL PERKULIAHAN -->
        <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                <span class="text-2xl group-hover:scale-110 transition-transform">📅</span>
            </div>
            <h3 class="font-bold text-slate-800 text-lg">Jadwal & Kelas</h3>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Buka kelas baru dan atur jadwal perkuliahan harian.</p>
            <div class="mt-6 flex gap-2">
                <a href="/admin/perkuliahan" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-emerald-200">
                    Atur Jadwal
                </a>
                <a href="/admin/perkuliahan/create" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-bold rounded-lg transition-colors">
                    +
                </a>
            </div>
        </div>

        <!-- DKBS MANAGEMENT -->
        <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-rose-600 group-hover:text-white transition-all">
                <span class="text-2xl group-hover:scale-110 transition-transform">📝</span>
            </div>
            <h3 class="font-bold text-slate-800 text-lg">Perwalian / DKBS</h3>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Verifikasi pengambilan mata kuliah mahasiswa.</p>
            <div class="mt-6 flex gap-2">
                <a href="/admin/dkbs" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-rose-200">
                    Manajemen DKBS
                </a>
            </div>
        </div>

        <!-- PENGUMUMAN -->
        <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition-all">
                <span class="text-2xl group-hover:scale-110 transition-transform">📢</span>
            </div>
            <h3 class="font-bold text-slate-800 text-lg">Pusat Pengumuman</h3>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Publikasikan informasi dan agenda kampus terbaru.</p>
            <div class="mt-6 flex gap-2">
                <a href="/admin/pengumuman" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-amber-200">
                    Kelola Berita
                </a>
                <a href="/admin/pengumuman/create" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-bold rounded-lg transition-colors">
                    +
                </a>
            </div>
        </div>

        <!-- PEMBAYARAN -->
        <div class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="w-12 h-12 bg-violet-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-violet-600 group-hover:text-white transition-all">
                <span class="text-2xl group-hover:scale-110 transition-transform">💰</span>
            </div>
            <h3 class="font-bold text-slate-800 text-lg">Keuangan / Billing</h3>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Kelola tagihan UKT dan pantau status pembayaran.</p>
            <div class="mt-6 flex gap-2">
                <a href="/admin/pembayaran" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm shadow-violet-200">
                    Atur Tagihan
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
