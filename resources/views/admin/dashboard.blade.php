@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 px-8 py-12 text-white shadow-2xl">
        <div class="relative z-10 max-w-2xl">
            <h1 class="text-3xl font-bold tracking-tight md:text-4xl">Selamat Datang kembali, <span class="text-blue-400 capitalize">{{ Auth::user()->name }}!</span></h1>
            <p class="mt-4 text-slate-300">Pusat kontrol administrasi akademik Edutrack. Pantau dan kelola seluruh data perkuliahan secara efisien dalam satu dashboard.</p>
        </div>
        <!-- Decorative background elements -->
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="absolute -bottom-20 right-40 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Stat Card 1 -->
        <div class="group relative flex items-center gap-5 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total Pengguna</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ \App\Models\User::count() }}</h3>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="group relative flex items-center gap-5 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Mata Kuliah</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ \App\Models\MataKuliah::count() }}</h3>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="group relative flex items-center gap-5 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 transition-colors group-hover:bg-indigo-600 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Kelas Dibuka</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ \App\Models\Perkuliahan::count() }}</h3>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="group relative flex items-center gap-5 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition-colors group-hover:bg-amber-600 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Total DKBS</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ \App\Models\Dkbs::count() }}</h3>
            </div>
        </div>
    </div>

    <!-- Navigation Quick Links -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Feature Card 1 -->
        <a href="/admin/mata-kuliah" class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-8 shadow-sm transition-all hover:shadow-2xl">
            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-slate-600 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h4 class="text-lg font-bold text-slate-900">Master Mata Kuliah</h4>
            <p class="mt-2 text-sm text-slate-500">Kelola master data mata kuliah, semester, dan beban SKS kurikulum.</p>
        </a>

        <!-- Feature Card 2 -->
        <a href="/admin/perkuliahan" class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-8 shadow-sm transition-all hover:shadow-2xl">
            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-indigo-600 transition-colors group-hover:bg-indigo-600 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h4 class="text-lg font-bold text-slate-900">Jadwal Perkuliahan</h4>
            <p class="mt-2 text-sm text-slate-500">Buka kelas baru, tentukan dosen pengampu, jam, dan alokasi ruangan.</p>
        </a>

        <!-- Feature Card 3 -->
        <a href="/admin/dkbs" class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-8 shadow-sm transition-all hover:shadow-2xl">
            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-emerald-600 transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h4 class="text-lg font-bold text-slate-900">DKBS Mahasiswa</h4>
            <p class="mt-2 text-sm text-slate-500">Daftarkan mahasiswa ke kelas yang dibuka dan kelola status perwalian.</p>
        </a>
    </div>
</div>
@endsection
