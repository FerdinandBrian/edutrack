@extends('layouts.admin')

@section('title', 'Manajemen Perkuliahan')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Jadwal Perkuliahan</h2>
            <p class="text-slate-500 mt-1">Kelola jadwal, pembagian kelas, dan dosen pengampu semester ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/perkuliahan/status" class="flex items-center gap-2 bg-white border border-slate-200 hover:border-blue-300 text-slate-600 hover:text-blue-600 px-6 py-3 rounded-xl font-semibold shadow-sm transition-all transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Cek Status Quota</span>
            </a>
            <a href="/admin/perkuliahan/create" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buka Kelas Baru</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Main Content -->
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/80">
                        <th class="px-8 py-5">Tipe</th>
                        <th class="px-6 py-5 text-center">Kelas</th>
                        <th class="px-6 py-5">Dosen Pengampu</th>
                        <th class="px-6 py-5">Jadwal & Ruang</th>
                        <th class="px-6 py-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php 
                        $lastBase = null; 
                        $lastJurusan = null;
                    @endphp
                    @forelse($data as $p)
                        @php
                            $currentBase = trim(str_replace(['(Teori)', '(Praktikum)'], '', $p->mataKuliah->nama_mk));
                            $currentJurusan = $p->mataKuliah->jurusan ?? 'Umum';
                            $isPraktikum = str_contains($p->mataKuliah->nama_mk, 'Praktikum');
                        @endphp

                        {{-- Jurusan Header --}}
                        @if($lastJurusan !== $currentJurusan)
                            <tr>
                                <td colspan="5" class="bg-indigo-900 px-8 py-3 sticky top-0 z-20">
                                    <div class="flex items-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <h3 class="font-bold text-white text-sm uppercase tracking-wider">{{ $currentJurusan }}</h3>
                                    </div>
                                </td>
                            </tr>
                            @php $lastBase = null; @endphp {{-- Reset Course Header trigger --}}
                        @endif

                        {{-- Group Header (Mata Kuliah) --}}
                        @if($lastBase !== $currentBase)
                            <tr>
                                <td colspan="5" class="bg-slate-50/90 px-8 py-3 border-y border-slate-200 backdrop-blur-sm sticky top-12 z-10">
                                    <div class="flex items-center gap-3">
                                        <div class="h-6 w-1 bg-blue-500 rounded-full"></div>
                                        <h3 class="font-bold text-slate-700 text-sm uppercase tracking-wide">{{ $currentBase }}</h3>
                                        <span class="text-xs text-slate-400 font-normal normal-case ml-auto">
                                            {{ $p->mataKuliah->kode_mk }} • {{ $p->mataKuliah->sks }} SKS
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        
                        @php 
                            $lastBase = $currentBase; 
                            $lastJurusan = $currentJurusan;
                        @endphp

                        <tr class="hover:bg-blue-50/40 transition-colors group">
                            {{-- Mata Kuliah (Modified to show only Type since Name is in header) --}}
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    {{-- Use Type as primary info here since Name is in header --}}
                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-blue-700 transition-colors">
                                        {{ $isPraktikum ? 'Praktikum' : 'Teori' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Kelas Badge --}}
                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl font-bold text-sm shadow-sm border
                                    {{ $p->kelas == 'A' ? 'bg-purple-50 text-purple-600 border-purple-100' : '' }}
                                    {{ $p->kelas == 'B' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }}
                                    {{ $p->kelas == 'C' ? 'bg-pink-50 text-pink-600 border-pink-100' : '' }}
                                    {{ !in_array($p->kelas, ['A','B','C']) ? 'bg-slate-50 text-slate-600 border-slate-100' : '' }}
                                ">
                                    {{ $p->kelas }}
                                </div>
                            </td>



                            {{-- Dosen --}}
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-500">
                                        {{ substr($p->dosen->nama, 0, 2) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-slate-700 w-48 truncate" title="{{ $p->dosen->nama }}">
                                            {{ $p->dosen->nama }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-mono">
                                            {{ $p->nip_dosen }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Jadwal & Ruang --}}
                            <td class="px-6 py-5">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="text-xs font-semibold">
                                            <span class="block">{{ $p->hari }}</span>
                                            <span class="block font-normal text-slate-500">{{ \Illuminate\Support\Str::substr($p->jam_mulai, 0, 5) }} - {{ \Illuminate\Support\Str::substr($p->jam_berakhir, 0, 5) }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">
                                            {{ $p->kode_ruangan }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="/admin/perkuliahan/{{ $p->id_perkuliahan }}/edit" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit Jadwal">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="/admin/perkuliahan/{{ $p->id_perkuliahan }}" method="POST" onsubmit="return confirm('Hapus jadwal perkuliahan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus Jadwal">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-slate-500 font-medium">Belum ada jadwal perkuliahan</p>
                                    <p class="text-slate-400 text-sm mt-1">Klik tombol "Buka Kelas Baru" untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
