@extends('layouts.mahasiswa')

@section('title','Pembayaran Tagihan')

@section('content')
<div class="max-w-md mx-auto">
    <!-- Payment Card -->
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden relative">
        <div class="absolute top-0 w-full h-1.5 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
        
        <!-- Header -->
        <div class="p-8 pb-6 text-center">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-blue-100 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            
            <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-2">Total Tagihan Anda</p>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</h2>
            
            <div class="inline-flex items-center gap-2 mt-4 px-4 py-1.5 bg-amber-50 text-amber-600 rounded-full text-xs font-bold ring-1 ring-amber-100">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                MENUNGGU PEMBAYARAN
            </div>
        </div>

        <!-- Payment Details -->
        <div class="mx-8 mb-8">
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/60 relative overflow-hidden group">
                <!-- Background Decoration -->
                <div class="absolute -right-6 -top-6 text-slate-200/40 group-hover:text-blue-100/50 transition-colors">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col gap-5">
                    <!-- Method -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Metode Pembayaran</label>
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-auto">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/2560px-Bank_Central_Asia.svg.png" class="h-full object-contain" alt="BCA">
                            </div>
                            <span class="text-sm font-bold text-slate-700">Virtual Account</span>
                        </div>
                    </div>

                    <!-- Separator -->
                    <div class="h-px bg-slate-200 w-full dashed"></div>

                    <!-- VA Number -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nomor Virtual Account</label>
                        <div class="flex items-center justify-between gap-2">
                             <div class="text-2xl font-mono font-bold text-slate-800 tracking-widest selection:bg-blue-100 selection:text-blue-700">
                                 {{ substr($paymentData['va'], 0, 4) }} {{ substr($paymentData['va'], 4, 4) }} {{ substr($paymentData['va'], 8, 4) }}
                             </div>
                             <button onclick="copyToClipboard('{{ $paymentData['va'] }}', this)" class="p-2 hover:bg-slate-200 rounded-lg transition-colors text-slate-500 hover:text-blue-600" title="Salin Nomor VA">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                             </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action -->
        <div class="p-8 pt-0">
            <a href="/mahasiswa/pembayaran/{{ $tagihan->id }}/simulation" class="block w-full bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-xl font-bold text-lg text-center shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-1 active:scale-[0.98] group relative overflow-hidden">
                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 pointer-events-none"></div>
                <span class="relative flex items-center justify-center gap-2">
                    Bayar Sekarang
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </span>
            </a>
            
            <p class="text-center text-xs text-slate-400 mt-5 leading-relaxed">
                Pembayaran akan diverifikasi secara otomatis.<br>
                Masalah pembayaran? <a href="/mahasiswa/bantuan?return_url={{ urlencode(request()->url()) }}" class="text-blue-600 hover:underline" title="Ke Pusat Bantuan">Hubungi Bantuan</a>
            </p>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalIcon = btn.innerHTML;
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>`;
            
            setTimeout(() => {
                btn.innerHTML = originalIcon;
            }, 2000);
        });
    }
</script>
@endsection
