@extends('layouts.mahasiswa')

@section('title', 'Daftar Nilai')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-100 bg-white">
        <h2 class="text-xl font-bold text-slate-800">Daftar Nilai Akademik</h2>
        <p class="text-sm text-slate-500 mt-1">Laporan nilai mata kuliah yang telah ditempuh</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-[11px] uppercase tracking-widest font-bold border-b border-slate-100">
                    <th class="px-8 py-4 w-16">#</th>
                    <th class="px-8 py-4">Mata Kuliah</th>
                    <th class="px-8 py-4 text-center">SKS</th>
                    <th class="px-8 py-4 text-center">Nilai Akhir</th>
                    <th class="px-8 py-4 text-right">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($data as $i => $row)
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-8 py-5 text-sm text-slate-400 font-mono">{{ $i + 1 }}</td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800">{{ $row->mataKuliah->nama_mk ?? 'Matakuliah tidak ditemukan' }}</span>
                            <span class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $row->kode_mk }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="text-sm font-medium text-slate-600">{{ $row->mataKuliah->sks ?? '-' }}</span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <div class="inline-block px-4 py-1.5 rounded-lg bg-indigo-50 border border-indigo-100">
                            <span class="text-sm font-bold text-indigo-700">{{ $row->nilai }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-right">
                        @php
                            $grade = '';
                            $color = 'text-slate-400';
                            if($row->nilai >= 85) { $grade = 'Sangat Memuaskan'; $color = 'text-emerald-600'; }
                            elseif($row->nilai >= 75) { $grade = 'Memuaskan'; $color = 'text-blue-600'; }
                            elseif($row->nilai >= 60) { $grade = 'Cukup'; $color = 'text-amber-600'; }
                            else { $grade = 'Perlu Perbaikan'; $color = 'text-rose-600'; }
                        @endphp
                        <span class="text-[10px] font-bold {{ $color }} uppercase tracking-tighter">{{ $grade }}</span>
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
                            <p class="text-slate-500 font-medium">Belum ada data nilai yang dipublikasikan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
