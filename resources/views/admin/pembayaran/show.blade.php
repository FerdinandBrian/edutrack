@extends('layouts.mahasiswa')

@section('title','Detail Pembayaran')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Detail Pembayaran</h2>

    <div class="mt-4 space-y-2">
        <div><strong>NRP:</strong> {{ $row->nrp }}</div>
        <div><strong>Nama:</strong> {{ optional($row->mahasiswa)->nama }}</div>
        <div><strong>Jumlah:</strong> {{ $row->jumlah ?? '-' }}</div>
        <div><strong>Status:</strong> {{ $row->status ?? '-' }}</div>
    </div>

    <div class="pt-4">
        <a href="/pembayaran" class="text-sm text-slate-600">Kembali</a>
    </div>
</div>
@endsection
