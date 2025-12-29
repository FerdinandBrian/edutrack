@extends(auth()->user()->role == 'dosen' ? 'layouts.dosen' : (auth()->user()->role == 'mahasiswa' ? 'layouts.mahasiswa' : 'layouts.admin'))

@section('title','Detail Presensi')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Detail Presensi</h2>

    <div class="mt-4 space-y-2">
        <div><strong>NRP:</strong> {{ $presensi->nrp }}</div>
        <div><strong>Nama:</strong> {{ optional($presensi->mahasiswa)->nama }}</div>
        <div><strong>Tanggal:</strong> {{ $presensi->tanggal }}</div>
        <div><strong>Status:</strong> {{ $presensi->status }}</div>
        <div><strong>Keterangan:</strong> {{ $presensi->keterangan }}</div>
        <div><strong>Jadwal:</strong> {{ optional($presensi->jadwal)->mata_kuliah ?? '-' }}</div>
    </div>

    <div class="pt-4">
        <a href="/presensi" class="text-sm text-slate-600">Kembali</a>
    </div>
</div>
@endsection
