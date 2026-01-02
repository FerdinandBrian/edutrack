@extends('layouts.mahasiswa')

@section('title','Mata Kuliah')

@section('content')

<!-- HERO SECTION -->
<div class="mb-8 bg-white rounded-2xl p-8 border border-slate-100 shadow-sm overflow-hidden relative">
    <div class="relative z-10">
        <h2 class="text-2xl font-bold text-slate-800">Daftar Mata Kuliah</h2>
        <p class="text-slate-500 mt-2 max-w-2xl">
            Berikut adalah daftar mata kuliah yang terdaftar dalam beban studi Anda. Informasi ini mencakup kode mata kuliah, nama, bobot SKS, dan semester.
        </p>
    </div>
    <!-- Decorative element -->
    <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
    <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
        <h3 class="font-semibold text-slate-700">Mata Kuliah Terdaftar</h3>
    </div>

    <div class="p-0">
        @if($data->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">📚</span>
                </div>
                <p class="text-slate-500">Belum ada mata kuliah yang diambil.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 uppercase text-[11px] font-bold tracking-wider border-b border-slate-100">
                            <th class="px-6 py-4">Kode MK</th>
                            <th class="px-6 py-4">Nama Mata Kuliah</th>
                            <th class="px-6 py-4 text-center">SKS</th>
                            <th class="px-6 py-4 text-center">Semester</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($data as $row)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs px-2 py-1 bg-slate-100 text-slate-600 rounded group-hover:bg-slate-200 transition-colors">
                                        {{ $row->kode_mk }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800">{{ optional($row->mataKuliah)->nama_mk ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 font-bold text-xs ring-1 ring-indigo-100">
                                        {{ optional($row->mataKuliah)->sks ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-600 font-medium">
                                    Semester {{ optional($row->mataKuliah)->semester ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
