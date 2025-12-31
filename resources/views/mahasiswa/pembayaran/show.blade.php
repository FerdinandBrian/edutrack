@extends('layouts.mahasiswa')

@section('title','Detail Pembayaran')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Breadcrumb / Back Navigation -->
    <div class="mb-6">
        <a href="/mahasiswa/pembayaran" class="inline-flex items-center text-sm text-slate-500 hover:text-indigo-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Riwayat
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 bg-white flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Invoice Tagihan</h2>
                <p class="text-sm text-slate-500 mt-1">NO. REF: #{{ str_pad($tagihan->id, 8, '0', STR_PAD_LEFT) }}</p>
            </div>
            
            @php
                $status = $tagihan->status ?? 'pending';
                $statusColor = match(strtolower($status)) {
                    'lunas' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                    'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                    default => 'bg-slate-50 text-slate-600 ring-slate-600/20',
                };
            @endphp
            <div class="{{ $statusColor }} px-4 py-1.5 rounded-full ring-1 ring-inset text-xs font-bold uppercase tracking-wide">
                {{ $tagihan->status }}
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-3">Ditagihkan Kepada</h3>
                    <p class="text-base font-bold text-slate-800">{{ optional($tagihan->mahasiswa)->nama ?? 'Mahasiswa' }}</p>
                    <p class="text-sm text-slate-600 mt-1">NRP: <span class="font-mono">{{ $tagihan->nrp }}</span></p>
                    <p class="text-sm text-slate-600 mt-1">{{ optional($tagihan->mahasiswa)->jurusan }}</p>
                </div>
                <div class="md:text-right">
                    <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-3">Detail Pembayaran</h3>
                    <p class="text-3xl font-bold text-slate-800 tracking-tight">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</p>
                    <p class="text-sm text-slate-500 mt-1">Batas Waktu: {{ $tagihan->created_at ? $tagihan->created_at->addDays(30)->format('d M Y') : '-' }}</p>
                </div>
            </div>

            <div class="rounded-xl bg-slate-50/50 border border-slate-100 p-6 mb-8">
                <div class="flex justify-between items-center mb-4 border-b border-slate-200 pb-4">
                    <span class="text-sm font-medium text-slate-600">Jenis Tagihan</span>
                    <span class="text-sm font-bold text-slate-800">{{ $tagihan->jenis }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-base font-bold text-slate-800">Total Tagihan</span>
                    <span class="text-xl font-bold text-indigo-700">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</span>
                </div>
            </div>

            @if(strtolower($tagihan->status ?? '') != 'lunas')
                <div class="flex flex-col md:flex-row gap-4 justify-end items-center">
                   <a href="/mahasiswa/pembayaran/{{ $tagihan->id }}/checkout" class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200 text-center">
                        Bayar Sekarang
                   </a>
                   <p class="text-xs text-slate-400 md:order-first">
                       *Konfirmasi pembayaran otomatis dicek dalam 1x24 jam
                   </p>
                </div>
            @else
                <div class="flex justify-end">
                    <div class="inline-flex items-center text-emerald-600 bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">Pembayaran Berhasil</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
