@extends('layouts.dosen')

@section('title', 'Daftar Nilai Mahasiswa')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manajemen Nilai Mahasiswa</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola nilai tugas 1-15, UTS, dan UAS dalam satu tempat</p>
        </div>
        <a href="/dosen/nilai/create" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-emerald-100 transition duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Rekap Nilai
        </a>
    </div>

    @if(session('success'))
        <div class="mx-8 mt-4 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-sm flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-[11px] uppercase tracking-widest font-bold border-b border-slate-100">
                    <th class="px-8 py-4">Mahasiswa</th>
                    <th class="px-8 py-4">Mata Kuliah</th>
                    <th class="px-8 py-4 text-center">UTS</th>
                    <th class="px-8 py-4 text-center">UAS</th>
                    <th class="px-8 py-4 text-center">Total</th>
                    <th class="px-8 py-4 text-center">Grade</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($data as $row)
                <tr class="hover:bg-emerald-50/30 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800">{{ $row->mahasiswa->nama }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">NRP: {{ $row->nrp }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-slate-700">{{ $row->mataKuliah->nama_mk }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $row->kode_mk }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="text-xs font-bold text-slate-600">{{ $row->uts }}</span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="text-xs font-bold text-slate-600">{{ $row->uas }}</span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="text-sm font-black text-emerald-600">{{ $row->nilai_total }}</span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="px-3 py-1 bg-slate-100 text-slate-800 text-xs font-black rounded-lg border border-slate-200 uppercase">
                            {{ $row->nilai_akhir }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center justify-center gap-3">
                            <a href="/dosen/nilai/{{ $row->id }}/edit" class="p-2 text-amber-500 hover:bg-amber-50 rounded-xl transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="/dosen/nilai/{{ $row->id }}" method="POST" onsubmit="return confirm('Hapus rekap nilai ini?');">
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
                    <td colspan="7" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253m0 0v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">Belum ada nilai yang diinput.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
