@extends('layouts.dosen')

@section('title','Input Nilai Mahasiswa')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Kelas & Input Nilai</h2>
            <p class="text-sm text-slate-500 mt-1">Pilih kelas untuk mengelola nilai mahasiswa.</p>
        </div>
        <!-- Optional: Global "Tambah Nilai" if manual entry is needed -->
        <!-- <a href="/dosen/nilai/create" ... > -->
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
                    <th class="px-6 py-4">Mata Kuliah</th>
                    <th class="px-6 py-4">Kelas</th>
                    <th class="px-6 py-4">Jadwal</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($classes as $index => $class)
                <tr class="hover:bg-slate-50/50 transition duration-150">
                    <td class="px-6 py-4 text-center text-slate-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ optional($class->mataKuliah)->nama_mk ?? 'Mata Kuliah' }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ optional($class->mataKuliah)->kode_mk }} • {{ optional($class->mataKuliah)->sks }} SKS</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Kelas {{ $class->kelas }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        <div>{{ $class->hari }}</div>
                        <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($class->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($class->jam_selesai)->format('H:i') }}</div>
                        <div class="text-xs text-slate-500">Ruang {{ optional($class->ruangan)->kode_ruangan ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="/dosen/nilai/kelas/{{ $class->id_perkuliahan }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition shadow-sm shadow-indigo-200">
                            Kelola Nilai
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                        Belum ada kelas yang diampu.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
