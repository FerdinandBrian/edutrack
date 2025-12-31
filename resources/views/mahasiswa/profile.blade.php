@extends('layouts.mahasiswa')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100">
        <!-- Header Profile -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-500 px-8 py-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 rounded-full bg-white text-blue-600 flex items-center justify-center text-4xl font-bold shadow-xl border-4 border-white/30">
                        {{ substr($user->nama, 0, 1) }}
                    </div>
                    <div class="text-white">
                        <h1 class="text-2xl font-bold">{{ $user->nama }}</h1>
                        <p class="text-blue-100 mt-1 flex items-center gap-2">
                            <span class="bg-blue-700/50 px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm">Mahasiswa</span>
                            <span class="text-sm opacity-90">{{ $user->email }}</span>
                        </p>
                    </div>
                </div>
                <a href="/mahasiswa/profile/edit" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm font-medium transition-colors border border-white/20 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Edit Profil
                </a>
            </div>
        </div>

        <!-- Body Profile -->
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Data Akun -->
                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        Informasi Akun
                    </h3>
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</label>
                            <p class="text-slate-800 font-medium">{{ $user->nama }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Email</label>
                            <p class="text-slate-800 font-medium">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">NRP (Nomor Induk Mahasiswa)</label>
                            <p class="text-slate-800 font-medium">{{ $user->mahasiswa->nrp ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Data Akademik -->
                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                         <span class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </span>
                        Data Akademik
                    </h3>
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100 space-y-4">
                         <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Jurusan</label>
                            <p class="text-slate-800 font-medium">{{ $user->mahasiswa->jurusan ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Alamat</label>
                            <p class="text-slate-800 font-medium">{{ $user->mahasiswa->alamat ?? '-' }}</p>
                        </div>
                         <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Bergabung</label>
                            <p class="text-slate-800 font-medium">{{ $user->created_at->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
