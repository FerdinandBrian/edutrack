@extends('layouts.mahasiswa')

@section('title','Jadwal')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Jadwal Kuliah</h2>
            <p class="text-sm text-slate-500 mt-1">Jadwal perkuliahan mingguan Anda</p>
        </div>
        <div>
             <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-blue-300 transition-colors">
                 <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Periode</span>
                 <form method="GET" action="{{ url()->current() }}">
                     <select name="tahun_ajaran" onchange="this.form.submit()" class="bg-transparent text-sm font-bold text-slate-700 focus:outline-none cursor-pointer hover:text-blue-600 transition-colors pr-1">
                         @foreach($tahun_ajarans as $ta)
                            <option value="{{ $ta }}" {{ $ta == $selectedTa ? 'selected' : '' }}>{{ $ta }}</option>
                         @endforeach
                     </select>
                 </form>
             </div>
        </div>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-sm text-slate-600 border-b">
                    <th class="py-4 font-bold uppercase tracking-wider text-xs">Mata Kuliah</th>
                    <th class="py-4 font-bold uppercase tracking-wider text-xs">Dosen Pengajar</th>
                    <th class="py-4 font-bold uppercase tracking-wider text-xs">Ruangan</th>
                    <th class="py-4 font-bold uppercase tracking-wider text-xs">Hari</th>
                    <th class="py-4 font-bold uppercase tracking-wider text-xs">Waktu</th>
                    <th class="py-4 font-bold uppercase tracking-wider text-xs text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedData = $data->groupBy(function ($item) {
                        return $item->perkuliahan->hari ?? 'Lainnya';
                    });
                @endphp

                @forelse($groupedData as $hari => $jadwals)
                    {{-- Separator Per Hari --}}
                    <tr>
                        <td colspan="6" class="py-3 px-2 bg-slate-100 font-bold text-slate-700 text-sm uppercase tracking-wide">
                            {{ $hari }}
                        </td>
                    </tr>

                    @foreach($jadwals as $row)
                        <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50 transition-colors">
                            <td class="py-4 align-top">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800">{{ $row->kode_mk }}</span>
                                    <span class="text-xs text-slate-500 mt-0.5">{{ optional($row->perkuliahan->mataKuliah)->nama_mk }}</span>
                                </div>
                            </td>
                            <td class="py-4 align-top text-sm text-slate-600">
                                {{ optional($row->perkuliahan->dosen)->nama ?? '-' }}
                            </td>
                            {{-- Kolom Ruangan --}}
                            <td class="py-4 align-top text-sm text-slate-600 font-medium">
                                {{ optional($row->perkuliahan->ruangan)->nama_ruangan ?? '-' }}
                            </td>
                            <td class="py-4 align-top text-sm font-medium text-slate-700">
                                {{ optional($row->perkuliahan)->hari ?? '-' }}
                            </td>
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
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500 italic">Belum ada data jadwal untuk semester ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
