@extends('layouts.mahasiswa')

@section('title','Daftar Presensi')

@section('content')

<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-slate-800">Daftar Presensi</h2>
        <div class="text-sm text-slate-500">
             <select class="border rounded px-2 py-1 text-sm bg-gray-50">
                 <option>Reguler Ganjil 2025/2026</option>
             </select>
        </div>
    </div>

    @if(session('success'))
        <div class="mt-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-blue-600 font-bold border-b-2 border-gray-100">
                    <th class="py-4 uppercase">Kode MK</th>
                    <th class="py-4 uppercase">Mata Kuliah</th>
                    <th class="py-4 uppercase text-center">SKS</th>
                    <th class="py-4 uppercase text-center">Alpha</th>
                    <th class="py-4 uppercase text-center">Hadir</th>
                    <th class="py-4 uppercase text-center">Pertemuan</th>
                    <th class="py-4 uppercase text-center">Persentase</th>
                    <th class="py-4 uppercase text-right px-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($summary as $row)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-4 text-slate-600">{{ $row->kode_mk }}</td>
                    <td class="py-4 font-medium text-slate-800">{{ $row->nama_mk }}</td>
                    <td class="py-4 text-center text-slate-600">{{ $row->sks }}</td>
                    <td class="py-4 text-center text-red-500 font-medium">{{ $row->alpha }}x</td>
                    <td class="py-4 text-center text-slate-600">{{ $row->hadir }}x</td>
                    <td class="py-4 text-center text-slate-600">{{ $row->total_pertemuan }}x</td>
                    <td class="py-4 text-center">
                        <span class="font-semibold text-blue-600">{{ number_format($row->persentase, 2) }}%</span>
                    </td>
                    <td class="py-4 text-right px-4">
                        <a href="/mahasiswa/presensi/{{ $row->jadwal_id }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-1.5 px-6 rounded transition-colors inline-block">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-slate-400 italic">Belum ada data perkuliahan atau presensi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
