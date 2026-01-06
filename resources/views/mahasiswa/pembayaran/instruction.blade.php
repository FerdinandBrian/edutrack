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
                <span class="text-sm font-medium text-slate-500">Virtual Account Edutrack</span>
                <span class="font-bold text-slate-800 text-xs px-2 py-1 bg-white border rounded">FIXED VA</span>
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
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                    <p class="text-sm text-slate-700 font-medium mb-2">Langkah Pembayaran:</p>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-slate-600">
                        <li>Gunakan aplikasi Bank pilihan Anda atau ATM</li>
                        <li>Pilih menu <strong>Transfer</strong> > <strong>Antar Bank</strong> (Jika bank Anda berbeda)</li>
                        <li>Masukkan nomor VA: <strong>{{ $paymentData['va'] }}</strong></li>
                        <li>Masukkan nominal sesuai tagihan: <strong>Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</strong></li>
                        <li>Periksa detail nama mahasiswa pada layar konfirmasi</li>
                        <li>Selesaikan transaksi dan simpan bukti pembayaran</li>
                    </ol>
                </div>
            </div>
            
            <form action="/mahasiswa/pembayaran/{{ $tagihan->id }}/confirm" method="POST" class="mt-8">
                @csrf
                <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-200 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Saya Sudah Membayar
                </button>
                <p class="text-center text-[10px] text-slate-400 mt-3 font-medium uppercase tracking-wide italic">Konfirmasi menggunakan Stored Procedure Database</p>
            </form>
        </div>
    </div>
</div>
@endsection
