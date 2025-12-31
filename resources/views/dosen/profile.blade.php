@extends('layouts.dosen')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100">
        <!-- Header Profile -->
        <div class="bg-gradient-to-r from-green-600 to-green-500 px-8 py-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 rounded-full bg-white text-green-600 flex items-center justify-center text-4xl font-bold shadow-xl border-4 border-white/30">
                        {{ substr($user->nama, 0, 1) }}
                    </div>
                    <div class="text-white">
                        <h1 class="text-2xl font-bold">{{ $user->nama }}</h1>
                        <p class="text-green-100 mt-1 flex items-center gap-2">
                            <span class="bg-green-700/50 px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm">Dosen</span>
                            <span class="text-sm opacity-90">{{ $user->email }}</span>
                        </p>
                    </div>
                </div>
                <a href="/dosen/profile/edit" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm font-medium transition-colors border border-white/20 flex items-center gap-2">
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
                        <span class="bg-green-100 text-green-600 p-2 rounded-lg">
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
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">NIP (Nomor Induk Pegawai)</label>
                            <p class="text-slate-800 font-medium">{{ $user->dosen->nip ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Data Jabatan -->
                <div>
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                         <span class="bg-green-100 text-green-600 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        Detail Dosen
                    </h3>
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100 space-y-4">
                         <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Kode Dosen</label>
                            <p class="text-slate-800 font-medium">{{ $user->dosen->kode_dosen ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Mata Kuliah Diampu</label>
                            <p class="text-slate-800 font-medium whitespace-pre-line">{{ $user->dosen->mata_kuliah ?? '-' }}</p>
                        </div>
                         <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Status Kepegawaian</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
