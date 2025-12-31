@extends('layouts.admin')

@section('title','Detail Pembayaran')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Detail Pembayaran</h2>

    <div class="mt-4 space-y-2">
        <div><strong>NRP:</strong> {{ $tagihan->nrp }}</div>
        <div><strong>Nama:</strong> {{ optional($tagihan->mahasiswa)->nama }}</div>
        <div><strong>Jenis:</strong> {{ $tagihan->jenis }}</div>
        <div><strong>Jumlah:</strong> Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</div>
        <div><strong>Status:</strong> {{ $tagihan->status ?? '-' }}</div>
    </div>

    <div class="pt-4">
        <a href="/admin/pembayaran" class="text-sm text-slate-600">Kembali</a>
    </div>
</div>
@endsection
