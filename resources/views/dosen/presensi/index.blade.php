@extends('layouts.dosen')

@section('title','Daftar Pertemuan Kelas')

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Pertemuan Kelas</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola presensi mahasiswa per pertemuan.</p>
        </div>
        <a href="/dosen/presensi/create" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-emerald-100 transition duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Buka Kelas Baru
        </a>
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
                    <th class="px-6 py-4">Tanggal & Waktu</th>
                    <th class="px-6 py-4">Mata Kuliah & Kelas</th>
                    <th class="px-6 py-4 text-center">Kehadiran</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sessions as $index => $session)
                <tr class="hover:bg-slate-50/50 transition duration-150">
                    <td class="px-6 py-4 text-center text-slate-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($session->tanggal)->translatedFormat('d F Y') }}</div>
                        <div class="text-xs text-slate-500 mt-1">
                            {{ \Carbon\Carbon::parse(optional($session->jadwal)->jam_mulai)->format('H:i') }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ optional(optional($session->jadwal)->mataKuliah)->nama_mk ?? 'Mata Kuliah' }}</div>
                        <div class="text-xs text-slate-500 mt-1">Kelas {{ optional($session->jadwal)->kelas ?? '-' }} • Ruang {{ optional(optional($session->jadwal)->ruangan)->kode_ruangan ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            @php
                                $hadir = \App\Models\Presensi::where('jadwal_id', $session->jadwal_id)->where('tanggal', $session->tanggal)->where('status','Hadir')->count();
                                $total = $session->total_students;
                                $persen = $total > 0 ? round(($hadir / $total) * 100) : 0;
                            @endphp
                            <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $persen }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-slate-600">{{ $hadir }}/{{ $total }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="/dosen/presensi/create?jadwal_id={{ $session->jadwal_id }}&tanggal={{ $session->tanggal }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-semibold hover:bg-indigo-100 transition">
                            Kelola Presensi
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                        Belum ada sesi perkuliahan. Klik "Buka Kelas Baru" untuk memulai.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
