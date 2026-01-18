@extends('layouts.admin')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="mb-6">
    <a href="/admin/pembayaran/student/{{ $tagihan->nrp }}" class="text-slate-500 hover:text-indigo-600 transition-colors flex items-center gap-2 text-sm font-medium group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Riwayat Mahasiswa
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl">
    <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-5 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-800">Detail Invoice Tagihan</h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">ID TAGIHAN: #{{ str_pad($tagihan->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="space-y-4">
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Informasi Mahasiswa</span>
                    <p class="text-sm font-bold text-slate-800">{{ $tagihan->mahasiswa->nama }}</p>
                    <p class="text-xs font-mono text-indigo-500">{{ $tagihan->nrp }}</p>
                </div>
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Virtual Account (BCA/Mandiri)</span>
                    <p class="inline-block bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg font-mono font-bold border border-indigo-100 text-sm">2911{{ $tagihan->nrp }}</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Pembayaran</span>
                    @php
                        $status = strtolower($tagihan->status ?? 'belum lunas');
                        $badgeClass = match($status) {
                            'lunas' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                            'pending' => 'bg-amber-100 text-amber-700 ring-amber-200',
                            default => 'bg-rose-100 text-rose-700 ring-rose-200',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase ring-1 {{ $badgeClass }}">
                        {{ $tagihan->status ?? 'Belum Lunas' }}
                    </span>
                </div>
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Batas Pembayaran</span>
                    <p class="text-sm font-bold {{ \Carbon\Carbon::parse($tagihan->batas_pembayaran)->isPast() && $tagihan->status != 'Lunas' ? 'text-rose-600' : 'text-slate-700' }}">
                        {{ \Carbon\Carbon::parse($tagihan->batas_pembayaran)->format('d F Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-slate-500">{{ $tagihan->jenis }}</span>
                <span class="text-sm font-bold text-slate-800">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</span>
            </div>
            <div class="border-t border-slate-200 pt-4 flex items-center justify-between">
                <span class="text-base font-bold text-slate-800">Total Tagihan</span>
                <span class="text-xl font-black text-indigo-600">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
             <a href="/admin/pembayaran/{{ $tagihan->id }}/edit" class="flex items-center gap-2 px-5 py-2.5 bg-amber-50 text-amber-700 rounded-xl text-xs font-bold hover:bg-amber-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Invoice
            </a>
            <form action="/admin/pembayaran/{{ $tagihan->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan ini?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-rose-50 text-rose-700 rounded-xl text-xs font-bold hover:bg-rose-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
