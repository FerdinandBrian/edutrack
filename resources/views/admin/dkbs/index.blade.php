@extends('layouts.admin')

@section('title', 'Daftar Kelas & Beban Studi')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex-1">
            <h2 class="text-xl font-bold text-slate-800">Manajemen DKBS</h2>
            <div class="flex items-center gap-4 mt-2">
                <form action="" method="GET" id="filter-form" class="flex items-center gap-3">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NRP atau Nama..." class="text-xs bg-slate-100 border-none rounded-lg pl-9 pr-4 py-2 text-slate-600 focus:ring-2 focus:ring-blue-500 transition w-64">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <select name="tahun_ajaran" onchange="document.getElementById('filter-form').submit()" class="text-xs font-bold bg-slate-100 border-none rounded-lg px-3 py-2 text-slate-600 focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">-- Semua Tahun Ajaran --</option>
                        @foreach($tahun_ajarans as $ta)
                            <option value="{{ $ta }}" {{ request('tahun_ajaran') == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                        @endforeach
                    </select>
                    @if(request('search') || request('tahun_ajaran'))
                        <a href="/admin/dkbs" class="text-[10px] font-bold text-red-500 hover:text-red-700 uppercase">Reset</a>
                    @endif
                </form>
            </div>
        </div>
        <a href="/admin/dkbs/create" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-blue-100 transition duration-300 whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah DKBS
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-[11px] uppercase tracking-widest font-bold border-b border-slate-100">
                    <th class="px-8 py-4">Mahasiswa</th>
                    <th class="px-8 py-4">Mata Kuliah & Kelas</th>
                    <th class="px-8 py-4">Jadwal & Ruangan</th>
                    <th class="px-8 py-4 text-center">Status</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($data as $d)
                <tr class="hover:bg-blue-50/30 transition-colors group">
                    <!-- Mahasiswa -->
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800">{{ $d->mahasiswa->nama }}</span>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $d->nrp }}</span>
                        </div>
                    </td>

                    <!-- MK & Kelas -->
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800">{{ $d->mataKuliah->nama_mk }}</span>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded font-bold border border-indigo-100 uppercase">
                                    Kelas {{ $d->perkuliahan->kelas ?? '-' }}
                                </span>
                                <span class="text-[11px] text-slate-400 font-medium">{{ $d->tahun_ajaran }}</span>
                            </div>
                        </div>
                    </td>

                    <!-- Jadwal -->
                    <td class="px-8 py-5">
                        @if($d->perkuliahan)
                            <div class="space-y-1">
                                <span class="text-xs text-slate-600 block">{{ \Illuminate\Support\Str::substr($d->perkuliahan->jam_mulai, 0, 5) }} - {{ \Illuminate\Support\Str::substr($d->perkuliahan->jam_berakhir, 0, 5) }}</span>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">
                                    {{ $d->perkuliahan->kode_ruangan }}
                                </span>
                            </div>
                        @else
                            <span class="text-xs text-slate-400">Jadwal TBD</span>
                        @endif
                    </td>

                    <!-- Status -->
                    <td class="px-8 py-5 text-center">
                        @php
                            $statusClass = match($d->status) {
                                'Terdaftar' => 'bg-emerald-100 text-emerald-700',
                                'Menunggu Antrean' => 'bg-amber-100 text-amber-700',
                                'Drop' => 'bg-red-100 text-red-700',
                                default => 'bg-slate-100 text-slate-700'
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $statusClass }}">
                            {{ strtoupper($d->status) }}
                        </span>
                    </td>

                    <!-- Aksi -->
                    <td class="px-8 py-5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="/admin/dkbs/{{ $d->id }}/edit" class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="/admin/dkbs/{{ $d->id }}" method="POST" onsubmit="return confirm('Hapus pendaftaran ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center text-slate-500">Data DKBS tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection