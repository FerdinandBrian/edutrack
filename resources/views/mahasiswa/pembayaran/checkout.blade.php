@extends('layouts.mahasiswa')

@section('title','Checkout Pembayaran')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="/mahasiswa/pembayaran/{{ $tagihan->id }}" class="inline-flex items-center text-sm text-slate-500 hover:text-indigo-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-lg font-bold text-slate-800">Detail Pembayaran</h2>
            <p class="text-sm text-slate-500">Gunakan Nomor Virtual Account berikut</p>
        </div>
        
        <form action="/mahasiswa/pembayaran/{{ $tagihan->id }}/checkout" method="POST">
            @csrf
            <div class="p-8">
                <!-- VA DISPLAY -->
                <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 text-center">
                    <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-2">Nomor Virtual Account Anda</p>
                    <div class="flex items-center justify-center gap-3">
                         <h3 class="text-3xl font-mono font-bold text-slate-800 tracking-tighter" id="vaNumber">{{ $va }}</h3>
                         <button type="button" onclick="copyVA('{{ $va }}', this)" class="p-2 bg-white rounded-lg border border-indigo-100 text-indigo-600 hover:text-indigo-800 hover:border-indigo-300 transition-all shadow-sm flex items-center gap-2 group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                            <span class="text-xs font-bold hidden group-[.copied]:inline">Tersalin!</span>
                         </button>
                    </div>
                    <p class="mt-4 text-[10px] text-slate-400 font-medium italic">Nomor ini bersifat tetap untuk seluruh pembayaran anda</p>
                </div>

                <script>
                    function copyVA(text, btn) {
                        navigator.clipboard.writeText(text).then(() => {
                            btn.classList.add('copied', 'text-emerald-600', 'border-emerald-200');
                            btn.classList.remove('text-indigo-600', 'border-indigo-100');
                            
                            setTimeout(() => {
                                btn.classList.remove('copied', 'text-emerald-600', 'border-emerald-200');
                                btn.classList.add('text-indigo-600', 'border-indigo-100');
                            }, 2000);
                        });
                    }
                </script>

                <div class="mt-8 space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Nama Mahasiswa</span>
                        <span class="font-bold text-slate-800">{{ optional($tagihan->mahasiswa)->nama }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">NRP</span>
                        <span class="font-mono font-bold text-slate-800">{{ $tagihan->nrp }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Keterangan</span>
                        <span class="font-medium text-slate-800">{{ $tagihan->jenis }}</span>
                    </div>
                </div>
            </div>
            
            <div class="p-6 bg-slate-50 border-t border-slate-100">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-sm font-medium text-slate-600">Total Tagihan</span>
                     <span class="text-xl font-bold text-indigo-700">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</span>
                </div>
                <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                    Lanjut ke Instruksi Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
