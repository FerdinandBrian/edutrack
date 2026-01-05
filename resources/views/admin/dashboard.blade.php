@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 to-slate-800 px-8 py-10 text-white shadow-xl shadow-slate-200">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <h1 class="text-3xl font-bold tracking-tight md:text-4xl text-white">
                    Selamat Datang, <span class="text-blue-400 capitalize">{{ Auth::user()->nama }}!</span>
                </h1>
                <p class="mt-4 text-slate-300 text-lg leading-relaxed">
                    Pusat kontrol administrasi Edutrack. Pantau statistik real-time dan kelola manajemen akademik dengan mudah.
                </p>
            </div>
            <div class="flex gap-3">
                <a href="/admin/profile" class="px-5 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl backdrop-blur-sm transition font-medium border border-white/10">
                    Edit Profil
                </a>
                <a href="/admin/pengumuman/create" class="px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl shadow-lg shadow-blue-900/50 transition font-bold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                    Buat Pengumuman
                </a>
            </div>
        </div>
        
        <!-- Decorative background elements -->
        <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-blue-600/20 to-transparent"></div>
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute -bottom-20 right-40 h-64 w-64 rounded-full bg-indigo-500/20 blur-3xl"></div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div class="group bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-lg transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="p-4 bg-blue-50 text-blue-600 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pengguna</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ \App\Models\User::count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="group bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-lg transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Mata Kuliah</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ \App\Models\MataKuliah::count() }}</h3>
                </div>
            </div>
        </div>
        
        <div class="group bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-lg transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas Aktif</p>
                    <h3 class="text-3xl font-bold text-slate-800">{{ \App\Models\Perkuliahan::count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Section Header -->
    <div class="flex items-center gap-4 px-2 pt-4">
        <h2 class="text-xl font-bold text-slate-800 whitespace-nowrap">Akses Cepat</h2>
        <div class="h-px bg-slate-200 w-full"></div>
    </div>

    <!-- Action Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- USER MANAGEMENT -->
        <a href="/admin/users" class="group bg-white rounded-3xl shadow-sm border border-slate-100 p-8 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col items-start relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity transform scale-150">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm group-hover:shadow-blue-200">
                <svg class="w-7 h-7 text-blue-600 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h3 class="font-bold text-slate-800 text-lg mb-2">Manajemen User</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">Kelola data seluruh civitas akademika kampus dengan lengkap dan terstruktur.</p>
            <span class="mt-auto text-blue-600 font-semibold text-sm group-hover:translate-x-1 transition-transform flex items-center gap-2">
                Akses Menu <span class="text-lg">→</span>
            </span>
        </a>

        <!-- MATA KULIAH MASTER -->
        <a href="/admin/mata-kuliah" class="group bg-white rounded-3xl shadow-sm border border-slate-100 p-8 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col items-start relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity transform scale-150">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm group-hover:shadow-indigo-200">
                <svg class="w-7 h-7 text-indigo-600 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h3 class="font-bold text-slate-800 text-lg mb-2">Master Mata Kuliah</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">Pusat data kurikulum dan registrasi mata kuliah baru.</p>
            <span class="mt-auto text-indigo-600 font-semibold text-sm group-hover:translate-x-1 transition-transform flex items-center gap-2">
                Akses Menu <span class="text-lg">→</span>
            </span>
        </a>

        <!-- JADWAL PERKULIAHAN -->
        <a href="/admin/perkuliahan" class="group bg-white rounded-3xl shadow-sm border border-slate-100 p-8 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col items-start relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity transform scale-150">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-sm group-hover:shadow-emerald-200">
                <svg class="w-7 h-7 text-emerald-600 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="font-bold text-slate-800 text-lg mb-2">Jadwal & Kelas</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">Manajemen pembukaan kelas dan penjadwalan ruang kuliah.</p>
            <span class="mt-auto text-emerald-600 font-semibold text-sm group-hover:translate-x-1 transition-transform flex items-center gap-2">
                Akses Menu <span class="text-lg">→</span>
            </span>
        </a>

        <!-- DKBS MANAGEMENT -->
        <a href="/admin/dkbs" class="group bg-white rounded-3xl shadow-sm border border-slate-100 p-8 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col items-start relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity transform scale-150">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-rose-600 group-hover:text-white transition-all shadow-sm group-hover:shadow-rose-200">
                <svg class="w-7 h-7 text-rose-600 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-bold text-slate-800 text-lg mb-2">Perwalian / DKBS</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">Kontrol rencana studi mahasiswa dan validasi perwalian.</p>
            <span class="mt-auto text-rose-600 font-semibold text-sm group-hover:translate-x-1 transition-transform flex items-center gap-2">
                Akses Menu <span class="text-lg">→</span>
            </span>
        </a>

        <!-- PENGUMUMAN -->
        <a href="/admin/pengumuman" class="group bg-white rounded-3xl shadow-sm border border-slate-100 p-8 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col items-start relative overflow-hidden">
            <!-- Background Shadow Icon (Bell) -->
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity transform scale-150">
                <svg class="w-32 h-32" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            </div>
            <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-600 group-hover:text-white transition-all shadow-sm group-hover:shadow-amber-200">
                <!-- Main Icon (Bell) -->
                <svg class="w-7 h-7 text-amber-600 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            </div>
            <h3 class="font-bold text-slate-800 text-lg mb-2">Pusat Pengumuman</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">Sebarkan informasi penting kepada seluruh civitas akademika.</p>
            <span class="mt-auto text-amber-600 font-semibold text-sm group-hover:translate-x-1 transition-transform flex items-center gap-2">
                Akses Menu <span class="text-lg">→</span>
            </span>
        </a>

        <!-- PEMBAYARAN -->
        <a href="/admin/pembayaran" class="group bg-white rounded-3xl shadow-sm border border-slate-100 p-8 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col items-start relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity transform scale-150">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-violet-600 group-hover:text-white transition-all shadow-sm group-hover:shadow-violet-200">
                <svg class="w-7 h-7 text-violet-600 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <h3 class="font-bold text-slate-800 text-lg mb-2">Keuangan / Billing</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">Monitoring tagihan mahasiswa dan status pembayaran UKT.</p>
            <span class="mt-auto text-violet-600 font-semibold text-sm group-hover:translate-x-1 transition-transform flex items-center gap-2">
                Akses Menu <span class="text-lg">→</span>
            </span>
        </a>

    </div>
</div>
@endsection
