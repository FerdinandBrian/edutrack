@extends('layouts.mahasiswa')

@section('title','Jadwal')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold mb-4">Jadwal Kuliah</h2>

    <div class="mt-4 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-sm text-slate-600 border-b">
                    <th class="py-4 font-bold uppercase tracking-wider text-xs">Mata Kuliah</th>
                    <th class="py-4 font-bold uppercase tracking-wider text-xs">Dosen Pengajar</th>
                    <th class="py-4 font-bold uppercase tracking-wider text-xs">Hari</th>
                    <th class="py-4 font-bold uppercase tracking-wider text-xs">Waktu</th>
                    <th class="py-4 font-bold uppercase tracking-wider text-xs text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
@forelse($data as $row)
                <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50 transition-colors">
                    <td class="py-4 align-top">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800">{{ $row->kode_mk }}</span>
                            <span class="text-xs text-slate-500 mt-0.5">{{ optional($row->perkuliahan->mataKuliah)->nama_mk }}</span>
                        </div>
                    </td>
                    <td class="py-4 align-top text-sm text-slate-600">{{ optional($row->perkuliahan->dosen)->nama ?? '-' }}</td>
                    <td class="py-4 align-top text-sm font-medium text-slate-700">{{ optional($row->perkuliahan)->hari ?? '-' }}</td>
                    <td class="py-4 align-top text-sm text-slate-600 font-mono">
                        @if($row->perkuliahan)
                            {{ \Carbon\Carbon::parse($row->perkuliahan->jam_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($row->perkuliahan->jam_berakhir)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="py-4 align-top text-right">
                        <a href="/mahasiswa/jadwal/{{ $row->id }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline">Lihat Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-500 italic">Belum ada data jadwal untuk semester ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
