@extends('layouts.admin')

@section('title', 'Daftar Dosen')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Data Dosen</h2>
            <p class="text-slate-500 mt-1">Lihat dan kelola informasi dosen pengampu.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="/admin/users/create?role=dosen" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Dosen Baru</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form action="/admin/dosen" method="GET" class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span class="font-semibold text-slate-600">Filter Jurusan:</span>
            </div>
            <select name="jurusan" onchange="this.form.submit()" class="border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 bg-slate-50 min-w-[250px]">
                <option value="">Semua Jurusan</option>
                @foreach($jurusans as $j)
                    <option value="{{ $j }}" {{ request('jurusan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/80">
                        <th class="px-8 py-5">Dosen</th>
                        <th class="px-6 py-5">Detail Kontak</th>
                        <th class="px-6 py-5">Fakultas & Jurusan</th>
                        <th class="px-6 py-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $lastJurusan = null; @endphp
                    @forelse($dosens as $d)
                        @php $currentJurusan = $d->jurusan ?? 'Belum Ditentukan'; @endphp
                        
                        @if($lastJurusan !== $currentJurusan)
                            <tr>
                                <td colspan="4" class="bg-indigo-900 px-8 py-3 sticky top-0 z-10">
                                    <div class="flex items-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <h3 class="font-bold text-white text-sm uppercase tracking-wider">{{ $currentJurusan }}</h3>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        
                        @php $lastJurusan = $currentJurusan; @endphp

                        <tr class="hover:bg-blue-50/40 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-100 uppercase">
                                        {{ substr($d->nama, 0, 2) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors">{{ $d->nama }}</span>
                                        <span class="text-[11px] text-slate-400 font-mono mt-0.5 tracking-tight">{{ $d->nip }}</span>
                                        <span class="inline-flex items-center w-fit px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-500 mt-1 border border-slate-200">
                                            {{ $d->jenis_kelamin }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs">{{ $d->email }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <span class="text-xs">{{ $d->no_telepon }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded w-fit">{{ $d->fakultas }}</span>
                                    <span class="text-[11px] text-slate-500">{{ $d->jurusan }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="/admin/users/{{ $d->user_id }}/edit" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit Data">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="/admin/users/{{ $d->user_id }}" method="POST" onsubmit="return confirm('Hapus data dosen ini? Menghapus dosen akan menghapus akun user terkait.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus Data">
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
                            <td colspan="4" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-slate-500 font-medium">Data dosen tidak ditemukan</p>
                                    @if(request('jurusan'))
                                        <p class="text-slate-400 text-sm mt-1">Coba ubah filter jurusan atau <a href="/admin/dosen" class="text-blue-500 hover:underline">reset filter</a></p>
                                    @endif
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
