@extends('layouts.admin')

@section('title','Manajemen Mata Kuliah')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <!-- Filter & Actions -->
    <div class="px-8 py-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Mata Kuliah (Master Data)</h2>
            <p class="text-sm text-slate-500 mt-1">Data master mata kuliah yang tersedia di kurikulum</p>
        </div>
        
        <div class="flex flex-col md:flex-row gap-3">
             <form method="GET" action="/admin/mata-kuliah" class="flex flex-col md:flex-row gap-3">
                <select name="jurusan" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" onchange="this.form.submit()">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j }}" {{ request('jurusan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
                <select name="semester" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" onchange="this.form.submit()">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $s)
                        <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endforeach
                </select>
                <select name="sifat" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 outline-none" onchange="this.form.submit()">
                    <option value="">Semua Sifat</option>
                    <option value="Wajib" {{ request('sifat') == 'Wajib' ? 'selected' : '' }}>Wajib</option>
                    <option value="Pilihan" {{ request('sifat') == 'Pilihan' ? 'selected' : '' }}>Pilihan</option>
                </select>
            </form>

            <a href="/admin/mata-kuliah/create" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-blue-100 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-[11px] uppercase tracking-widest font-bold border-b border-slate-100">
                    <th class="px-8 py-4">Mata Kuliah</th>
                    <th class="px-8 py-4">SKS / Sifat</th>
                    <th class="px-8 py-4 text-center">Semester</th>
                    <th class="px-8 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @php $lastJurusan = null; @endphp
                @forelse($data as $mk)
                
                @php $currentJurusan = $mk->jurusan ?? 'Jurusan Belum Diisi'; @endphp

                @if($lastJurusan !== $currentJurusan)
                    <tr>
                        <td colspan="4" class="bg-indigo-900 px-8 py-3 border-y border-indigo-100 sticky top-0 backdrop-blur-sm z-10">
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

                <tr class="hover:bg-blue-50/30 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition">{{ $mk->nama_mk }}</span>
                            <span class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $mk->kode_mk }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col gap-1">
                            <span class="inline-flex items-center w-fit px-2.5 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                {{ $mk->sks }} SKS
                            </span>
                            <span class="inline-flex items-center w-fit px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $mk->sifat == 'Pilihan' ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                {{ $mk->sifat ?? 'Wajib' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="text-sm font-medium text-slate-600">{{ $mk->semester }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center justify-center gap-3">
                            <a href="/admin/mata-kuliah/{{ $mk->kode_mk }}/edit" class="p-2 text-amber-500 hover:bg-amber-50 rounded-xl transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="/admin/mata-kuliah/{{ $mk->kode_mk }}" method="POST" onsubmit="return confirm('Hapus master data mata kuliah ini?');">
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
                    <td colspan="4" class="px-8 py-20 text-center text-slate-500">Belum ada master data mata kuliah.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
