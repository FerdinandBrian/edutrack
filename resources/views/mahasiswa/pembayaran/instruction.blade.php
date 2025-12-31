@extends('layouts.mahasiswa')

@section('title','Instruksi Pembayaran')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- HEADER -->
        <div class="p-8 border-b border-slate-100 text-center">
            <p class="text-sm text-slate-500 mb-2">Total Pembayaran</p>
            <h2 class="text-3xl font-bold text-slate-800">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</h2>
            <div class="inline-flex items-center gap-2 mt-4 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-medium">
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                Menunggu Pembayaran
            </div>
        </div>

        <!-- VA SECTION -->
        <div class="p-6 bg-slate-50/50 border-b border-slate-100">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-slate-500">{{ $paymentData['method'] }}</span>
                @php
                    $bank = explode(' ', $paymentData['method'])[0];
                    $logo = match($bank) {
                        'BCA' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/2560px-Bank_Central_Asia.svg.png',
                        'Mandiri' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/2560px-Bank_Mandiri_logo_2016.svg.png',
                        'BRI' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/BANK_BRI_logo.svg/2560px-BANK_BRI_logo.svg.png',
                        default => ''
                    };
                @endphp
                @if($logo)
                    <img src="{{ $logo }}" class="h-6 object-contain" alt="{{ $bank }}" referrerpolicy="no-referrer" onerror="this.onerror=null; this.parentNode.innerHTML='<span class=\'font-bold text-slate-800\'>'+this.alt+'</span>'"> 
                @else
                    <span class="font-bold text-slate-300">{{ $bank }}</span>
                @endif
            </div>
            <div class="flex justify-between items-center bg-white border border-slate-200 rounded-xl p-4">
                <div>
                     <p class="text-xs text-slate-400 mb-1">Nomor Virtual Account</p>
                     <p class="text-xl font-mono font-bold text-slate-800 tracking-wider">{{ $paymentData['va'] }}</p>
                </div>
                <button onclick="copyToClipboard('{{ $paymentData['va'] }}', this)" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold transition-all">
                    Salin
                </button>
            </div>
        </div>
        
        <script>
            function copyToClipboard(text, btn) {
                navigator.clipboard.writeText(text).then(() => {
                    const originalText = btn.innerText;
                    btn.innerText = 'Tersalin!';
                    btn.classList.add('text-emerald-600');
                    btn.classList.remove('text-indigo-600');
                    
                    setTimeout(() => {
                        btn.innerText = originalText;
                        btn.classList.remove('text-emerald-600');
                        btn.classList.add('text-indigo-600');
                    }, 2000);
                });
            }
        </script>
        
        <!-- INSTRUCTION -->
        <div class="p-6">
            <h3 class="font-bold text-slate-800 mb-4">Cara Pembayaran</h3>
            <div class="space-y-4">
                <details class="group rounded-xl border border-slate-200 open:bg-slate-50 open:border-indigo-100">
                    <summary class="flex justify-between items-center p-4 cursor-pointer list-none font-medium text-slate-700">
                        <span>ATM {{ explode(' ', $paymentData['method'])[0] }}</span>
                        <span class="transition group-open:rotate-180">
                            <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                        </span>
                    </summary>
                    <div class="text-sm text-slate-600 px-4 pb-4">
                        <ol class="list-decimal list-inside space-y-2">
                            <li>Masukkan kartu ATM dan PIN</li>
                            <li>Pilih menu <strong>Transaksi Lainnya</strong></li>
                            <li>Pilih menu <strong>Transfer</strong> > <strong>Ke Rekening Virtual Account</strong></li>
                            <li>Masukkan nomor VA: <strong>{{ $paymentData['va'] }}</strong></li>
                            <li>Periksa detail pembayaran</li>
                            <li>Pilih <strong>YA</strong> untuk konfirmasi</li>
                        </ol>
                    </div>
                </details>
                
                 <details class="group rounded-xl border border-slate-200 open:bg-slate-50 open:border-indigo-100">
                    <summary class="flex justify-between items-center p-4 cursor-pointer list-none font-medium text-slate-700">
                        <span>M-Banking</span>
                        <span class="transition group-open:rotate-180">
                            <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                        </span>
                    </summary>
                    <div class="text-sm text-slate-600 px-4 pb-4">
                        <ol class="list-decimal list-inside space-y-2">
                            <li>Login ke aplikasi Mobile Banking</li>
                            <li>Pilih menu <strong>m-Transfer</strong></li>
                            <li>Pilih <strong>Virtual Account</strong></li>
                            <li>Masukkan nomor VA: <strong>{{ $paymentData['va'] }}</strong></li>
                            <li>Periksa detail pembayaran</li>
                            <li>Masukkan PIN m-Banking</li>
                        </ol>
                    </div>
                </details>
            </div>
            
            <form action="/mahasiswa/pembayaran/{{ $tagihan->id }}/confirm" method="POST" class="mt-8">
                @csrf
                <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-200 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Saya Sudah Membayar
                </button>
                <p class="text-center text-xs text-slate-400 mt-3">Klik tombol di atas jika sudah berhasil transfer</p>
            </form>
        </div>
    </div>
</div>
@endsection
