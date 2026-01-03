@extends('layouts.dosen')

@section('title', 'Presensi Kelas')

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    
    <!-- STEP 1: PILIH JADWAL & TANGGAL -->
    <div class="p-8 border-b border-slate-100 bg-slate-50/50">
        <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm">1</span>
            Pilih Kelas & Tanggal
        </h2>
        
        <form action="/dosen/presensi/create" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Jadwal Perkuliahan</label>
                <select name="jadwal_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition bg-white" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($jadwals as $j)
                        <option value="{{ $j->id_perkuliahan }}" {{ request('jadwal_id') == $j->id_perkuliahan ? 'selected' : '' }}>
                            {{ $j->hari }}, {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ optional($j->mataKuliah)->nama_mk }} (Kelas {{ $j->kelas }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Tanggal Pertemuan</label>
                <input type="date" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition bg-white" required>
            </div>

            <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-xl shadow-lg shadow-slate-200 transition-all flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                Buka Kelas
            </button>
        </form>
    </div>

    <!-- STEP 2: DAFTAR MAHASISWA -->
    @if(request('jadwal_id') && $selectedJadwal)
    <div class="p-8">
        <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm">2</span>
            Daftar Hadir Mahasiswa
        </h2>

        <div class="mb-6 bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
             <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
             <div>
                 <p class="text-sm font-semibold text-blue-800">Kelas: {{ optional($selectedJadwal->mataKuliah)->nama_mk }} ({{ $selectedJadwal->kelas }})</p>
                 <p class="text-xs text-blue-600 mt-1">Tanggal: {{ \Carbon\Carbon::parse(request('tanggal'))->translatedFormat('l, d F Y') }}</p>
             </div>
        </div>

        <form action="/{{ auth()->user()->role }}/presensi" method="POST">
            @csrf
            <input type="hidden" name="bulk_presensi" value="1">
            <input type="hidden" name="jadwal_id" value="{{ request('jadwal_id') }}">
            <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">

            <div class="overflow-x-auto border border-slate-200 rounded-xl mb-8">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs">
                        <tr>
                            <th class="px-6 py-4 border-b border-slate-200">Mahasiswa</th>
                            <th class="px-6 py-4 border-b border-slate-200 text-center w-64">Status Kehadiran</th>
                            <th class="px-6 py-4 border-b border-slate-200 w-64">Keterangan (Opsional)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($mahasiswas as $index => $m)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $m->nama }}</p>
                                <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $m->nrp }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2 bg-slate-100 p-1 rounded-lg">
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="presensi[{{$m->nrp}}][status]" value="Hadir" class="peer sr-only" checked>
                                        <div class="px-3 py-1.5 rounded-md text-center text-xs font-semibold text-slate-500 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:shadow-sm transition-all">
                                            Hadir
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="presensi[{{$m->nrp}}][status]" value="Izin" class="peer sr-only">
                                        <div class="px-3 py-1.5 rounded-md text-center text-xs font-semibold text-slate-500 peer-checked:bg-amber-500 peer-checked:text-white peer-checked:shadow-sm transition-all">
                                            Izin
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="presensi[{{$m->nrp}}][status]" value="Absen" class="peer sr-only">
                                        <div class="px-3 py-1.5 rounded-md text-center text-xs font-semibold text-slate-500 peer-checked:bg-red-500 peer-checked:text-white peer-checked:shadow-sm transition-all">
                                            Absen
                                        </div>
                                    </label>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="presensi[{{$m->nrp}}][keterangan]" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:border-emerald-500 outline-none transition" placeholder="Catatan...">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500 italic">
                                Tidak ada mahasiswa yang terdaftar di kelas ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="/{{ auth()->user()->role }}/presensi" class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold shadow-lg shadow-emerald-200 hover:bg-emerald-700 hover:-translate-y-0.5 transition-all">
                    Simpan Data Presensi
                </button>
            </div>
        </form>
    </div>
    @elseif(request('jadwal_id'))
     <div class="p-8 text-center text-slate-500">
        Jadwal tidak ditemukan.
    </div>
    @else
    <div class="p-12 text-center">
        <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        </div>
        <h3 class="text-slate-800 font-bold mb-1">Siap Mengajar?</h3>
        <p class="text-slate-500 text-sm">Silakan pilih jadwal dan tanggal pertemuan di atas untuk mulai mengisi presensi.</p>
    </div>
    @endif

</div>
@endsection
