@extends(auth()->user()->role == 'dosen' ? 'layouts.dosen' : (auth()->user()->role == 'mahasiswa' ? 'layouts.mahasiswa' : 'layouts.admin'))

@section('title','Detail Nilai')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Detail Nilai</h2>

    <div class="mt-4 space-y-2">
        <div><strong>NRP:</strong> {{ $row->nrp }}</div>
        <div><strong>Nama:</strong> {{ optional($row->mahasiswa)->nama }}</div>
        <div><strong>Mata Kuliah:</strong> {{ $row->kode_mk }}</div>
        <div><strong>Nilai:</strong> {{ $row->nilai }}</div>
    </div>

    <div class="pt-4">
        <a href="/nilai" class="text-sm text-slate-600">Kembali</a>
    </div>
</div>
@endsection
