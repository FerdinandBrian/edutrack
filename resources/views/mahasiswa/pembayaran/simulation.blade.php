<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BCA Payment Gateway Simulation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f0f2f5; }
        .bca-blue { background-color: #0060AF; }
        .bca-blue-text { color: #0060AF; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-md border border-slate-200">
        <!-- Header BCA -->
        <div class="bca-blue p-4 flex justify-between items-center">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/2560px-Bank_Central_Asia.svg.png" alt="BCA Logo" class="h-8 bg-white p-1 rounded">
            <div class="text-right text-white">
                <p class="text-xs font-light">Layanan Pembayaran Online</p>
                <p class="font-bold text-sm">Virtual Account</p>
            </div>
        </div>

        <!-- content -->
        <div class="p-6 space-y-6">
            
            <div class="border-b border-gray-200 pb-4">
                <p class="text-xs text-slate-500 uppercase font-bold mb-1">Total Tagihan</p>
                <h2 class="text-2xl font-bold bca-blue-text">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</h2>
            </div>

            <div class="space-y-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Merchant</span>
                    <span class="font-bold text-slate-700">UNIVERSITAS CLOUD</span>
                </div>
                <div class="flex justify-between">
                     <span class="text-slate-500">Nomor Virtual Account</span>
                     <span class="font-mono font-bold text-slate-700">{{ $paymentData['va'] }}</span>
                </div>
                <div class="flex justify-between">
                     <span class="text-slate-500">Waktu Transaksi</span>
                     <span class="text-slate-700">{{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') }}</span>
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mt-4">
                <label class="block text-xs font-bold bca-blue-text uppercase mb-2">Konfirmasi PIN</label>
                <input type="password" id="mpin" maxlength="6" placeholder="Masukkan 6 digit PIN" class="w-full border border-gray-300 rounded p-3 text-center text-lg font-bold text-slate-700 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
            </div>

            <!-- Actions -->
            <form id="paymentForm" action="/mahasiswa/pembayaran/{{ $tagihan->id }}/confirm" method="POST" class="mt-8">
                @csrf
                <button type="button" onclick="processPayment()" id="payBtn" class="w-full py-3 bca-blue hover:bg-blue-800 text-white font-bold rounded shadow-lg transition-all transform active:scale-95">
                    PROSES PEMBAYARAN
                </button>
                <div class="mt-4 text-center">
                    <a href="/mahasiswa/pembayaran/{{ $tagihan->id }}/instruction" class="text-xs text-slate-400 hover:text-slate-600 font-medium">Batalkan Transaksi</a>
                </div>
            </form>
        </div>
        
        <!-- Processing Overlay -->
        <div id="processingOverlay" class="absolute inset-0 bg-white/90 backdrop-blur-sm z-50 hidden flex-col items-center justify-center p-8 text-center">
            <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-700 rounded-full animate-spin mb-4"></div>
            <h3 class="text-lg font-bold bca-blue-text">Memproses Transaksi...</h3>
            <p class="text-slate-500 text-xs mt-2">Jangan tutup halaman ini.</p>
        </div>
        
        <!-- Success Overlay -->
        <div id="successOverlay" class="absolute inset-0 bg-white z-50 hidden flex-col items-center justify-center p-8 text-center">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-6 shadow-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-1">Transaksi Berhasil</h3>
            <p class="text-slate-500 text-sm mb-6">Pembayaran Anda telah diterima.</p>
            <p class="text-[10px] text-slate-400">Redirecting...</p>
        </div>

    </div>

    <script>
        function processPayment() {
            const mpin = document.getElementById('mpin').value;
            if(mpin.length < 6) {
                alert('Silahkan masukkan PIN anda.');
                return;
            }

            const overlay = document.getElementById('processingOverlay');
            const successOverlay = document.getElementById('successOverlay');
            const form = document.getElementById('paymentForm');

            // Show Processing
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');

            // Simulate Network Delay (3 seconds)
            setTimeout(() => {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                
                // Show Success
                successOverlay.classList.remove('hidden');
                successOverlay.classList.add('flex');
                
                // Submit Form after 2 more seconds
                setTimeout(() => {
                    form.submit();
                }, 2000);
            }, 3000);
        }
    </script>
</body>
</html>
