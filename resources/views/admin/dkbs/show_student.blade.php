@extends('layouts.admin')

@section('title', 'Detail DKBS Mahasiswa')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header / Student Profile -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 rounded-full bg-slate-100 border-2 border-slate-200 flex items-center justify-center text-2xl font-bold text-slate-400">
                {{ substr($mahasiswa->nama, 0, 2) }}
            </div>
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-2xl font-bold text-slate-800">{{ $mahasiswa->nama }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-emerald-100 text-emerald-700 tracking-wide">Aktif</span>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-slate-500 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884.356 1.633.927 2.196M12 4v16m0 0l-3-3m3 3l3-3" /></svg>
                        NRP: <span class="font-mono text-slate-700">{{ $mahasiswa->nrp }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-500 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Total SKS Kumulatif: <span class="text-blue-600 font-bold ml-1">{{ $totalSksKumulatif }} SKS</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-500 text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        {{ $mahasiswa->jurusan }}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex gap-3">
             <a href="/admin/dkbs" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-semibold hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
            <a href="/admin/dkbs/create?nrp={{ $mahasiswa->nrp }}" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-blue-200 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Mata Kuliah
            </a>
        </div>
    </div>

    <!-- Course List -->
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/80">
                        <th class="px-8 py-5">Mata Kuliah</th>
                        <th class="px-6 py-5 text-center">Kelas</th>
                        <th class="px-6 py-5">Dosen</th>
                        <th class="px-6 py-5">Jadwal</th>
                        <th class="px-6 py-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $lastTa = null; @endphp
                    @forelse($dkbs as $item)
                        @php 
                            $currentTa = $item->tahun_ajaran; 
                            $isPraktikum = str_contains($item->mataKuliah->nama_mk, 'Praktikum');
                        @endphp

                        @if($lastTa !== $currentTa)
                            <tr>
                                <td colspan="5" class="bg-slate-50/90 px-8 py-3 border-y border-slate-200 sticky top-0 backdrop-blur-sm z-10">
                                    <div class="flex items-center justify-between whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                            <h3 class="font-bold text-slate-600 text-sm uppercase tracking-wide">{{ $currentTa }}</h3>
                                        </div>
                                        <div class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg border border-blue-100 text-[10px] font-bold uppercase tracking-wider">
                                            Total: {{ $dkbs->where('tahun_ajaran', $currentTa)->sum('mataKuliah.sks') }} SKS
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @php $lastTa = $currentTa; @endphp

                        <tr class="hover:bg-blue-50/40 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-800 group-hover:text-blue-700 transition-colors">
                                        {{ $item->mataKuliah->nama_mk }}
                                    </span>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded border border-slate-200 font-mono">
                                            {{ $item->kode_mk }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">
                                            {{ $item->mataKuliah->sks }} SKS
                                        </span>
                                        @if($isPraktikum)
                                            <span class="text-[10px] bg-amber-50 text-amber-600 border border-amber-100 px-1.5 py-0.5 rounded font-bold uppercase">Praktikum</span>
                                        @elseif(str_contains($item->mataKuliah->nama_mk, 'Teori'))
                                            <span class="text-[10px] bg-blue-50 text-blue-600 border border-blue-100 px-1.5 py-0.5 rounded font-bold uppercase">Teori</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-bold text-sm bg-slate-50 text-slate-600 border border-slate-200">
                                    {{ optional($item->perkuliahan)->kelas ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-semibold text-slate-700">{{ optional(optional($item->perkuliahan)->dosen)->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                 @if($item->perkuliahan)
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5 text-slate-600">
                                            <span class="text-xs font-bold">{{ $item->perkuliahan->hari }}</span>
                                            <span class="text-[10px] text-slate-400 font-mono">
                                                {{ \Illuminate\Support\Str::substr($item->perkuliahan->jam_mulai, 0, 5) }}-{{ \Illuminate\Support\Str::substr($item->perkuliahan->jam_berakhir, 0, 5) }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] bg-emerald-50 text-emerald-600 px-1.5 py-0.5 rounded border border-emerald-100 font-bold uppercase">
                                            {{ optional($item->perkuliahan->ruangan)->kode_ruangan }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Jadwal dihapus</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center">
                                <form action="/admin/dkbs/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus mata kuliah ini dari rencana studi?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-16 text-center text-slate-500 italic">
                                Belum ada mata kuliah yang diambil.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
