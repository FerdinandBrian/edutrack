@extends('layouts.admin')

@section('title','Manajemen Perkuliahan')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Jadwal Perkuliahan</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola pembukaan kelas, dosen pengampu, dan jadwal per semester</p>
        </div>
        <a href="/admin/perkuliahan/create" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-indigo-100 transition duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Buka Kelas Baru
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-[11px] uppercase tracking-widest font-bold border-b border-slate-100">
                    <th class="px-8 py-4">Mata Kuliah & Kelas</th>
                    <th class="px-8 py-4">Dosen Pengampu</th>
                    <th class="px-8 py-4">Waktu & Ruangan</th>
                    <th class="px-8 py-4">Tahun Ajaran</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($data as $p)
                <tr class="hover:bg-indigo-50/30 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800">{{ $p->mataKuliah->nama_mk }}</span>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-mono">{{ $p->kode_mk }}</span>
                                <span class="text-[10px] bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded font-bold border border-indigo-100">Kelas {{ $p->kelas }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold text-slate-700">{{ $p->dosen->nama }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">NIP: {{ $p->nip_dosen }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="space-y-1">
                            <span class="text-xs text-slate-600 block">{{ \Illuminate\Support\Str::substr($p->jam_mulai, 0, 5) }} - {{ \Illuminate\Support\Str::substr($p->jam_berakhir, 0, 5) }}</span>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100 uppercase">
                                {{ $p->kode_ruangan }}
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-xs font-medium text-slate-500">{{ $p->tahun_ajaran }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center justify-center gap-3">
                            <a href="/admin/perkuliahan/{{ $p->id_perkuliahan }}/edit" class="p-2 text-amber-500 hover:bg-amber-50 rounded-xl transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="/admin/perkuliahan/{{ $p->id_perkuliahan }}" method="POST" onsubmit="return confirm('Hapus jadwal perkuliahan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition-colors">
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
                    <td colspan="5" class="px-8 py-20 text-center text-slate-500">Belum ada perkuliahan yang terjadwal.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
