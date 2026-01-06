@extends('layouts.admin')

@section('title','Manajemen DKBS')

@section('content')
<div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
    <!-- Header -->
    <div class="px-8 py-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manajemen DKBS</h2>
            <p class="text-sm text-slate-500 mt-1">Pilih mahasiswa untuk mengelola Rencana Studi mereka.</p>
        </div>
        
        <!-- Search Form -->
        <form action="/admin/dkbs" method="GET" class="relative w-full md:w-72">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NRP atau Nama..." 
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all text-sm">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </form>
    </div>

    @if(session('success'))
        <div class="mx-8 mt-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl flex items-center gap-3">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto mt-4 px-8 pb-8">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/80">
                    <th class="px-6 py-5 w-12 text-center">No</th>
                    <th class="px-6 py-5">Mahasiswa</th>
                    <th class="px-6 py-5 text-center">NRP</th>
                    <th class="px-6 py-5 text-center">Status</th>
                    <th class="px-6 py-5 text-right w-48">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php $lastJurusan = null; @endphp
                @forelse($students as $index => $student)
                
                @php 
                    $currentJurusan = $student->jurusan ?? 'Jurusan Belum Diisi'; 
                @endphp

                @if($lastJurusan !== $currentJurusan)
                    <tr>
                        <td colspan="5" class="bg-indigo-900/5 px-8 py-3 border-y border-indigo-100 sticky top-0 backdrop-blur-sm z-10">
                            <div class="flex items-center gap-3">
                                <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                                <h3 class="font-bold text-indigo-900 text-sm uppercase tracking-wider">{{ $currentJurusan }}</h3>
                            </div>
                        </td>
                    </tr>
                @endif
                
                @php $lastJurusan = $currentJurusan; @endphp

                <tr class="hover:bg-indigo-50/20 transition-colors group">
                    <td class="px-6 py-5 text-center text-slate-400 text-xs font-mono">{{ $index + 1 }}</td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-sm font-bold text-slate-500 uppercase">
                                {{ substr($student->nama, 0, 2) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $student->nama }}</span>
                                <span class="text-[10px] text-slate-400">{{ $student->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="font-mono font-medium text-slate-600 bg-slate-50 border border-slate-200 px-2 py-1 rounded text-xs select-all">
                            {{ $student->nrp }}
                        </span>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Aktif
                        </span>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <a href="/admin/dkbs/student/{{ $student->nrp }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 hover:shadow-indigo-200 transform hover:-translate-y-0.5">
                            Kelola DKBS
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-20 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="text-slate-500 font-medium">Belaum ada data mahasiswa.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection