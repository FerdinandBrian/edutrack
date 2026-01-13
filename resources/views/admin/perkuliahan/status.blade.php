@extends('layouts.admin')

@section('title', 'Status Quota Kelas')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div>
            <div class="flex items-center gap-2 text-blue-600 mb-2">
                <a href="/admin/perkuliahan" class="text-sm font-semibold hover:underline">Jadwal Perkuliahan</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-sm text-slate-400">Status Quota</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Status Pembukaan Kelas</h2>
            <p class="text-slate-500 mt-1">Laporan jumlah mahasiswa per kelas berdasarkan batas minimal (5 orang).</p>
        </div>
        
        <!-- Filter TA -->
        <form action="/admin/perkuliahan/status" method="GET" class="flex items-center gap-3">
            <select name="tahun_ajaran" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                @foreach($allTa as $ta)
                    <option value="{{ $ta }}" {{ $tahunAjaran == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
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
                        <th class="px-6 py-5 text-center">Jumlah Mahasiswa</th>
                        <th class="px-6 py-5 text-center">Status Pembukaan</th>
                        <th class="px-6 py-5">Progress Quota</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php 
                        $lastBase = null; 
                        $lastJurusan = null;
                    @endphp
                    @forelse($data as $row)
                        @php
                            $currentBase = trim(str_replace(['(Teori)', '(Praktikum)'], '', $row->nama_mk));
                            $currentJurusan = $row->jurusan ?? 'Umum';
                            $isPraktikum = str_contains($row->nama_mk, 'Praktikum');
                            
                            $isOpened = ($row->jumlah_mahasiswa >= 5);
                            $progress = min(($row->jumlah_mahasiswa / 5) * 100, 100);
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
                            @php $lastBase = null; @endphp
                        @endif

                        {{-- Group Header (Mata Kuliah) --}}
                        @if($lastBase !== $currentBase)
                            <tr>
                                <td colspan="5" class="bg-slate-50/90 px-8 py-3 border-y border-slate-200 backdrop-blur-sm sticky top-12 z-10">
                                    <div class="flex items-center gap-3">
                                        <div class="h-6 w-1 bg-blue-500 rounded-full"></div>
                                        <h3 class="font-bold text-slate-700 text-sm uppercase tracking-wide">{{ $currentBase }}</h3>
                                        <span class="text-xs text-slate-400 font-normal normal-case ml-auto">
                                            {{ $row->kode_mk }} • {{ $row->sks }} SKS
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
                            {{-- Tipe --}}
                            <td class="px-8 py-5">
                                <span class="text-sm font-semibold text-slate-600 group-hover:text-blue-700 transition-colors">
                                    {{ $isPraktikum ? 'Praktikum' : 'Teori' }}
                                </span>
                            </td>

                            {{-- Kelas --}}
                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl font-bold text-sm shadow-sm border
                                    {{ $row->kelas == 'A' ? 'bg-purple-50 text-purple-600 border-purple-100' : '' }}
                                    {{ $row->kelas == 'B' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }}
                                    {{ $row->kelas == 'C' ? 'bg-pink-50 text-pink-600 border-pink-100' : '' }}
                                    {{ !in_array($row->kelas, ['A','B','C']) ? 'bg-slate-50 text-slate-600 border-slate-100' : '' }}
                                ">
                                    {{ $row->kelas }}
                                </div>
                            </td>

                            {{-- Jumlah Mahasiswa --}}
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-lg font-black {{ $isOpened ? 'text-blue-600' : 'text-amber-600' }}">
                                            {{ $row->jumlah_mahasiswa }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-bold">/ {{ $row->kapasitas }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter">Terdaftar</span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    @if($isOpened)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-black border border-emerald-100 shadow-sm uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            DIBUKA
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-[10px] font-black border border-amber-100 shadow-sm uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            DITUTUP
                                        </span>
                                    @endif
                                    
                                    @php $sisa = $row->kapasitas - $row->jumlah_mahasiswa; @endphp
                                    <span class="text-[11px] font-bold {{ $sisa > 0 ? 'text-indigo-600' : 'text-red-500' }}">
                                        {{ $sisa > 0 ? $sisa . ' Slot Sisa' : 'Kelas Penuh' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Progress Bar --}}
                            <td class="px-6 py-5">
                                <div class="w-40">
                                    @php 
                                        $capProgress = ($row->kapasitas > 0) ? min(($row->jumlah_mahasiswa / $row->kapasitas) * 100, 100) : 0;
                                    @endphp
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[10px] font-bold text-slate-400">{{ round($capProgress) }}% Kapasitas</span>
                                        <span class="text-[10px] font-bold text-slate-400">Limit: {{ $row->kapasitas }}</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden shadow-inner p-0.5">
                                        <div class="h-full rounded-full transition-all duration-1000 {{ $capProgress >= 100 ? 'bg-red-500' : ($capProgress >= 80 ? 'bg-orange-400' : 'bg-blue-500') }}" style="width: {{ $capProgress }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="font-medium">Tidak ada data kelas ditemukan</p>
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
