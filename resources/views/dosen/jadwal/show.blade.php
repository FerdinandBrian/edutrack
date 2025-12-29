@extends(auth()->user()->role == 'dosen' ? 'layouts.dosen' : (auth()->user()->role == 'mahasiswa' ? 'layouts.mahasiswa' : 'layouts.admin'))

@section('title','Detail Jadwal')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Detail Jadwal</h2>

    <div class="mt-4 space-y-2">
        <div><strong>Kode MK:</strong> {{ $row->kode_mk }}</div>
        <div><strong>Dosen:</strong> {{ optional($row->dosen)->nama ?? $row->nidn }}</div>
        <div><strong>Hari:</strong> {{ $row->hari }}</div>
        <div><strong>Jam:</strong> {{ $row->jam }}</div>
    </div>

    <div class="pt-4">
        <a href="/jadwal" class="text-sm text-slate-600">Kembali</a>
    </div>
</div>
@endsection
