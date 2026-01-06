@extends('layouts.admin')

@section('title', 'Notifikasi Pembayaran')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Aktivitas Pembayaran</h2>
            <p class="text-slate-500 text-sm mt-1">Pantau semua transaksi yang sedang berjalan dan terbaru</p>
        </div>
        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
    </div>

    <div class="relative pl-4 border-l-2 border-slate-100 space-y-8">
        @forelse($notifications as $item)
            <div class="relative">
                <!-- Timeline Dot -->
                <div class="absolute -left-[21px] top-1 h-3 w-3 rounded-full border-2 border-white 
                    {{ strtolower($item->status) === 'lunas' ? 'bg-emerald-500 ring-4 ring-emerald-50' : 'bg-amber-500 ring-4 ring-amber-50' }}">
                </div>

                <div class="flex items-start justify-between group p-4 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold font-mono text-slate-400">{{ $item->updated_at->format('H:i') }}</span>
                            <span class="text-xs font-medium text-slate-400">• {{ $item->updated_at->format('d M Y') }}</span>
                        </div>
                        
                        <h4 class="text-sm font-bold text-slate-800">
                            {{ optional($item->mahasiswa)->nama ?? 'Mahasiswa' }}
                            <span class="font-normal text-slate-500">
                                @if(strtolower($item->status) === 'lunas')
                                    telah melunasi tagihan
                                @else
                                    memiliki tagihan baru / status pending
                                @endif
                            </span>
                        </h4>
                        
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $item->jenis }} 
                            @if($item->tipe_pembayaran == 3)
                                (Cicilan Ke-{{ $item->cicilan_ke }})
                            @endif
                        </p>
                        
                        <p class="text-xs font-mono font-medium text-slate-400 mt-2">
                            Ref: #{{ str_pad($item->id, 8, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>

                    <div class="text-right">
                        <span class="block text-sm font-bold text-slate-800">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase 
                            {{ strtolower($item->status) === 'lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $item->status }}
                        </span>
                        <div class="mt-2">
                             <a href="/admin/pembayaran/{{ $item->id }}" class="text-xs text-blue-600 hover:underline font-medium">Lihat Detail &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <p class="text-slate-500 font-medium">Belum ada aktivitas pembayaran.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
