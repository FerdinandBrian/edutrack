@extends('layouts.admin')

@section('title', 'Manajemen Pembayaran')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    {{-- Header --}}
    <div class="p-6 border-b border-slate-100 bg-slate-50/30">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Manajemen Pembayaran</h2>
                <p class="text-sm text-slate-500 mt-1">Pilih mahasiswa per jurusan untuk mengelola tagihan dan pembayaran.</p>
            </div>
            <a href="/admin/pembayaran/create" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-200 transition-all group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Tagihan Baru
            </a>
        </div>

        {{-- Filters --}}
        <div class="mt-6 flex flex-col md:flex-row gap-4 px-1">
            <form action="/admin/pembayaran" method="GET" class="flex-1 flex gap-4">
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari NRP atau Nama Mahasiswa..." 
                           class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                </div>
                
                <select name="jurusan" onchange="this.form.submit()" 
                        class="block w-48 py-2.5 px-4 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j }}" {{ request('jurusan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
                
                @if(request('search') || request('jurusan'))
                    <a href="/admin/pembayaran" class="px-4 py-2.5 text-rose-500 hover:bg-rose-50 rounded-xl text-sm font-bold transition-all flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Content --}}
    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-[10px] uppercase font-bold tracking-widest text-slate-400 border-b border-slate-100 bg-slate-50/50">
                    <th class="py-4 px-6">Mahasiswa</th>
                    <th class="py-4 px-4">Jurusan</th>
                    <th class="py-4 px-4 text-center">Status Pembayaran</th>
                    <th class="py-4 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @php $currentJurusan = null; @endphp
                @forelse($students as $student)
                    @if($student->jurusan !== $currentJurusan && !request('jurusan'))
                        @php $currentJurusan = $student->jurusan; @endphp
                        <tr class="bg-indigo-50/40">
                            <td colspan="4" class="py-3 px-6 border-y border-indigo-100/50">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-1.5 rounded-full bg-indigo-600"></div>
                                    <span class="text-[10px] font-black text-indigo-900 uppercase tracking-widest">{{ $currentJurusan ?? 'TANPA JURUSAN' }}</span>
                                </div>
                            </td>
                        </tr>
                    @endif

                    <tr class="hover:bg-slate-50/80 transition-all group">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-bold text-slate-500 text-sm group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm flex-shrink-0">
                                    {{ substr($student->nama, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 text-sm truncate max-w-[200px]" title="{{ $student->nama }}">{{ $student->nama }}</div>
                                    <div class="text-[11px] font-mono text-slate-400 group-hover:text-indigo-400 transition-colors">{{ $student->nrp }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="text-xs font-semibold text-slate-600">{{ $student->jurusan }}</span>
                        </td>
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            @php
                                $tagihanSummary = \App\Models\Tagihan::where('nrp', $student->nrp)
                                    ->selectRaw('count(*) as total, sum(case when status != "Lunas" then 1 else 0 end) as unpaid')
                                    ->first();
                                $totalTagihan = $tagihanSummary->total;
                                $unpaid = $tagihanSummary->unpaid;
                            @endphp

                            @if($totalTagihan > 0)
                                @if($unpaid == 0)
                                    <span class="inline-block bg-emerald-100 text-emerald-700 text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-tighter ring-1 ring-emerald-200 whitespace-nowrap">Lunas Semua</span>
                                @else
                                    <span class="inline-block bg-rose-100 text-rose-700 text-[9px] font-black px-2 py-0.5 rounded uppercase tracking-tighter ring-1 ring-rose-200 whitespace-nowrap">{{ $unpaid }} Belum Lunas</span>
                                @endif
                            @else
                                <span class="text-slate-300 text-[9px] font-bold uppercase tracking-tighter">- No Bills -</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap">
                            <a href="/admin/pembayaran/student/{{ $student->nrp }}" 
                               class="inline-flex items-center gap-1.5 bg-white hover:bg-indigo-600 border border-slate-200 hover:border-indigo-600 text-slate-600 hover:text-white px-4 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm active:scale-95 whitespace-nowrap">
                                Kelola Pembayaran
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <span class="text-slate-400 font-medium italic">Tidak ada mahasiswa ditemukan.</span>
                                @if(request('search') || request('jurusan'))
                                    <a href="/admin/pembayaran" class="text-indigo-600 font-bold text-xs hover:underline mt-1">Hapus semua filter</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @forelse($students as $student)
                @empty
                @endforelse
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
