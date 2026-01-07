@extends('layouts.mahasiswa')

@section('title', 'DKBS')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-white">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Kelas & Beban Studi (DKBS)</h2>
            <p class="text-sm text-slate-500 mt-1">Daftar mata kuliah yang Anda kontrak pada semester ini</p>
        </div>
        <div class="flex items-center gap-4">
            @if(isset($totalSks))
            <div class="bg-blue-600 px-4 py-2 rounded-xl text-white shadow-lg shadow-blue-100 flex flex-col items-center">
                <span class="text-[10px] uppercase font-bold opacity-80 leading-none mb-1">Total SKS</span>
                <span class="text-xl font-black leading-none">{{ $totalSks }}</span>
            </div>
            @endif
            <div class="text-right">
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full border border-blue-100 uppercase tracking-wider">
                    Resmi / Valid
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mx-8 mt-4 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-sm flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-[11px] uppercase tracking-widest font-bold border-b border-slate-100">
                    <th class="px-8 py-4">Mata Kuliah</th>
                    <th class="px-8 py-4">Kelas</th>
                    <th class="px-8 py-4">Jadwal</th>
                    <th class="px-8 py-4 text-center">SKS</th>
                    <th class="px-8 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($data as $row)
                <tr class="hover:bg-blue-50/30 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800">{{ $row->mataKuliah->nama_mk }}</span>
                            <span class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $row->kode_mk }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="inline-block bg-slate-100 text-slate-700 text-xs font-bold px-3 py-1 rounded-lg border border-slate-200">
                            {{ $row->perkuliahan->kelas ?? '-' }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            @if($row->perkuliahan)
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $row->perkuliahan->hari }}
                                </div>
                                <div class="text-[10px] text-slate-500 mt-1 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ \Illuminate\Support\Str::substr($row->perkuliahan->jam_mulai, 0, 5) }} - {{ \Illuminate\Support\Str::substr($row->perkuliahan->jam_berakhir, 0, 5) }}
                                </div>
                                @if($row->perkuliahan->ruangan)
                                <div class="mt-1.5">
                                    <span class="text-[10px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded border border-emerald-100 font-bold uppercase">
                                        {{ $row->perkuliahan->ruangan->kode_ruangan }}
                                    </span>
                                </div>
                                @endif
                            @else
                                <span class="text-[10px] text-rose-500 italic">Jadwal belum tersedia</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="text-sm font-bold text-slate-700">{{ $row->mataKuliah->sks }}</span>
                    </td>
                    <td class="px-8 py-5">
                        @php
                            $statusClass = match($row->status) {
                                'Terdaftar' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'Menunggu Antrean' => 'bg-amber-50 text-amber-600 border-amber-100',
                                'Drop' => 'bg-rose-50 text-rose-600 border-rose-100',
                                default => 'bg-slate-50 text-slate-600 border-slate-100'
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                            {{ strtoupper($row->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">Belum ada kontrak mata kuliah.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection