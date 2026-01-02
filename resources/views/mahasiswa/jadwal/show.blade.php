@extends(auth()->user()->role == 'dosen' ? 'layouts.dosen' : (auth()->user()->role == 'mahasiswa' ? 'layouts.mahasiswa' : 'layouts.admin'))

@section('title','Detail Jadwal')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Detail Jadwal</h2>

    <div class="mt-4 space-y-2">
        <div><strong>Kode MK:</strong> {{ $row->kode_mk }}</div>
        <div><strong>Mata Kuliah:</strong> {{ optional($row->perkuliahan->mataKuliah)->nama_mk }}</div>
        <div><strong>Dosen:</strong> {{ optional($row->perkuliahan->dosen)->nama ?? '-' }}</div>
        <div><strong>Hari:</strong> {{ optional($row->perkuliahan)->hari ?? '-' }}</div>
        <div><strong>Jam:</strong> 
            @if($row->perkuliahan)
                {{ \Carbon\Carbon::parse($row->perkuliahan->jam_mulai)->format('H:i') }} - 
                {{ \Carbon\Carbon::parse($row->perkuliahan->jam_berakhir)->format('H:i') }}
            @else
                -
            @endif
        </div>
        <div><strong>Ruangan:</strong> {{ optional($row->perkuliahan->ruangan)->nama_ruangan ?? '-' }} ({{ optional($row->perkuliahan)->kode_ruangan ?? '' }})</div>
    </div>

    <div class="pt-4">
        <a href="/mahasiswa/jadwal" class="text-sm text-slate-600">Kembali</a>
    </div>
</div>
@endsection
