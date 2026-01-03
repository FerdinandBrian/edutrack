@extends('layouts.mahasiswa')

@section('title', 'Detail Presensi - ' . ($perkuliahan->mataKuliah->nama_mk ?? ''))

@section('content')

<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Presensi</h2>
            <p class="text-sm text-slate-500 mt-1">
                {{ $perkuliahan->mataKuliah->kode_mk ?? '' }} - {{ $perkuliahan->mataKuliah->nama_mk ?? '' }} ({{ $perkuliahan->kelas ?? '' }})
            </p>
        </div>
        <a href="/mahasiswa/presensi" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 font-semibold border-b">
                    <th class="py-4 w-12 px-2">No</th>
                    <th class="py-4">NRP</th>
                    <th class="py-4">Nama</th>
                    <th class="py-4">Tanggal</th>
                    <th class="py-4">Status</th>
                    <th class="py-4">Keterangan</th>
                    <th class="py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($data as $i => $row)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-2 text-slate-500">{{ $i + 1 }}</td>
                    <td class="py-4 text-slate-700">{{ $row->nrp }}</td>
                    <td class="py-4 font-medium text-slate-800">{{ optional($row->mahasiswa)->nama }}</td>
                    <td class="py-4 text-slate-600">{{ \Carbon\Carbon::parse($row->tanggal)->format('Y-m-d') }}</td>
                    <td class="py-4">
                        @if($row->status == 'Hadir')
                        <span class="text-slate-700">Hadir</span>
                        @elseif($row->status == 'Absen')
                        <span class="text-red-500 font-medium">Alpha</span>
                        @else
                        <span class="text-yellow-600 font-medium">{{ $row->status }}</span>
                        @endif
                    </td>
                    <td class="py-4 text-slate-500 italic">{{ $row->keterangan ?? '-' }}</td>
                    <td class="py-4 text-right">
                        <a href="#" class="text-blue-500 hover:underline text-xs">Lihat Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-slate-400 italic">Belum ada riwayat presensi untuk matakuliah ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
