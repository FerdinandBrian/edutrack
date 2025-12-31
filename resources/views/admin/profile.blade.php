@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100">
        <!-- Header Profile -->
        <div class="bg-gradient-to-r from-red-600 to-red-500 px-8 py-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 rounded-full bg-white text-red-600 flex items-center justify-center text-4xl font-bold shadow-xl border-4 border-white/30">
                        {{ substr($user->nama, 0, 1) }}
                    </div>
                    <div class="text-white">
                        <h1 class="text-2xl font-bold">{{ $user->nama }}</h1>
                        <p class="text-red-100 mt-1 flex items-center gap-2">
                            <span class="bg-red-700/50 px-3 py-1 rounded-full text-xs font-medium backdrop-blur-sm">Admin</span>
                            <span class="text-sm opacity-90">{{ $user->email }}</span>
                        </p>
                    </div>
                </div>
                <a href="/admin/profile/edit" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg backdrop-blur-sm font-medium transition-colors border border-white/20 flex items-center gap-2">
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
                         <span class="bg-red-100 text-red-600 p-2 rounded-lg">
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
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Kode Admin</label>
                            <p class="text-slate-800 font-medium">{{ $user->admin->kode_admin ?? '-' }}</p>
                        </div>
                         <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Role</label>
                            <p class="text-slate-800 font-medium capitalize">{{ $user->role }}</p>
                        </div>
                    </div>
                </div>

                <!-- Statistik System (Placeholder) -->
                <div>
                     <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                         <span class="bg-red-100 text-red-600 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                        Akses Sistem
                    </h3>
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                        <p class="text-sm text-slate-600 mb-4">
                            Sebagai administrator, Anda memiliki akses penuh ke manajemen pengguna, jadwal perkuliahan, dan sistem pembayaran.
                        </p>
                        <div class="flex items-center gap-2 text-green-600 font-medium text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Super Admin Privileges Active
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
