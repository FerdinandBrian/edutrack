@extends('layouts.dosen')

@section('title','Jadwal')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Jadwal</h2>
            <a href="/dosen/jadwal/create" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-emerald-100 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Jadwal
            </a>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-sm text-slate-600 border-b">
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Mata Kuliah & Kelas</th>
                    <th class="py-3 px-4">Hari & Jam</th>
                    <th class="py-3 px-4">Ruangan</th>
                    <th class="py-3 px-4 w-24 text-center">SKS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $row)
                <tr class="border-b hover:bg-slate-50 transition">
                    <td class="py-3 px-4 text-slate-500">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4">
                        <div class="font-bold text-slate-800">{{ optional($row->mataKuliah)->nama_mk ?? 'Matkul' }}</div>
                        <div class="text-xs text-slate-500">Kelas {{ $row->kelas }} • {{ $row->kode_mk }}</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                             <span class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded text-xs font-bold">{{ $row->hari }}</span>
                             <span class="text-sm font-semibold text-slate-700">
                                {{ \Carbon\Carbon::parse($row->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($row->jam_berakhir)->format('H:i') }}
                             </span>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-sm font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded border border-emerald-100">{{ $row->kode_ruangan }}</span>
                    </td>
                     <td class="py-3 px-4 text-center">
                        <span class="text-sm font-bold text-slate-600">{{ optional($row->mataKuliah)->sks ?? 0 }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-500">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Belum ada jadwal mengajar yang ditentukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
