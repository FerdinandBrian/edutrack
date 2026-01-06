@extends('layouts.dosen')

@section('title', 'Kelola Nilai Kelas')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <a href="/dosen/nilai" class="text-slate-400 hover:text-blue-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <h2 class="text-xl font-bold text-slate-800">Daftar Nilai Mahasiswa</h2>
            </div>
            <p class="text-sm text-slate-500 ml-7">{{ optional($jadwal->mataKuliah)->nama_mk }} - Kelas {{ $jadwal->kelas }}</p>
        </div>
        <!-- 
        <a href="/dosen/nilai/create" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-emerald-100 transition duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Input Massal (Coming Soon)
        </a> 
        -->
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 w-12 text-center">No</th>
                    <th class="px-6 py-4">Nama Mahasiswa</th>
                    <th class="px-6 py-4">NRP</th>
                    <th class="px-6 py-4 text-center">Tugas (Avg)</th>
                    <th class="px-6 py-4 text-center">UTS</th>
                    <th class="px-6 py-4 text-center">UAS</th>
                    <th class="px-6 py-4 text-center">Total</th>
                    <th class="px-6 py-4 text-center">Grade</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($students as $index => $dkbs)
                    @php
                        $student = $dkbs->mahasiswa;
                        $nilai = $grades->get($student->nrp);
                        
                        // Calculate task avg manually for display if needed, or assume it's stored/calced
                        // Nilai model has many 'p1'...'p15' fields.
                        $tugasAvg = 0;
                        if($nilai) {
                             $cols = ['p1','p2','p3','p4','p5','p6','p7','p9','p10','p11','p12','p13','p14','p15'];
                             $sum = 0;
                             $count = 0;
                             foreach($cols as $c) {
                                 if(!is_null($nilai->$c)) {
                                     $sum += $nilai->$c;
                                     $count++;
                                 }
                             }
                             $tugasAvg = $count > 0 ? round($sum / $count, 2) : 0;
                        }
                    @endphp
                <tr class="hover:bg-slate-50/50 transition duration-150">
                    <td class="px-6 py-4 text-center text-slate-500">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $student->nama }}</div>
                    </td>
                    <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                        {{ $student->nrp }}
                    </td>
                    <td class="px-6 py-4 text-center text-slate-600">
                        {{ $nilai ? $tugasAvg : '-' }}
                    </td>
                    <td class="px-6 py-4 text-center text-slate-600">
                        {{ $nilai ? $nilai->uts : '-' }}
                    </td>
                    <td class="px-6 py-4 text-center text-slate-600">
                        {{ $nilai ? $nilai->uas : '-' }}
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-slate-800">
                        {{ $nilai ? $nilai->nilai_total : '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($nilai)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-bold text-xs
                                {{ $nilai->nilai_akhir == 'A' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $nilai->nilai_akhir == 'AB' ? 'bg-emerald-50 text-emerald-600' : '' }}
                                {{ $nilai->nilai_akhir == 'B' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $nilai->nilai_akhir == 'BC' ? 'bg-blue-50 text-blue-600' : '' }}
                                {{ $nilai->nilai_akhir == 'C' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $nilai->nilai_akhir == 'D' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $nilai->nilai_akhir == 'E' ? 'bg-red-100 text-red-700' : '' }}
                            ">
                                {{ $nilai->nilai_akhir }}
                            </span>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($nilai)
                            <a href="/dosen/nilai/{{ $nilai->id }}/edit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 text-xs font-semibold hover:bg-amber-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                Edit
                            </a>
                        @else
                            <a href="/dosen/nilai/create?nrp={{ $student->nrp }}&kode_mk={{ $jadwal->kode_mk }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 text-xs font-semibold hover:bg-blue-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Input
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center gap-2">
                            <p>Belum ada mahasiswa di kelas ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
