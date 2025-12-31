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
            <h2 class="text-lg font-bold text-slate-800">Metode Pembayaran</h2>
            <p class="text-sm text-slate-500">Pilih salah satu metode pembayaran</p>
        </div>
        
        <form action="/mahasiswa/pembayaran/{{ $tagihan->id }}/checkout" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <!-- BCA -->
                <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                    <input type="radio" name="payment_method" value="BCA Virtual Account" checked class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                    <div class="ml-4 flex-1">
                        <span class="block font-bold text-slate-800">BCA Virtual Account</span>
                        <span class="block text-sm text-slate-500">Cek otomatis</span>
                    </div>
                    <div class="w-10 h-10 bg-blue-600/10 rounded-lg flex items-center justify-center text-blue-700 font-bold text-xs">BCA</div>
                </label>
                
                <!-- MANDIRI -->
                <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                    <input type="radio" name="payment_method" value="Mandiri Virtual Account" class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                    <div class="ml-4 flex-1">
                        <span class="block font-bold text-slate-800">Mandiri Virtual Account</span>
                        <span class="block text-sm text-slate-500">Cek otomatis</span>
                    </div>
                    <div class="w-10 h-10 bg-yellow-600/10 rounded-lg flex items-center justify-center text-yellow-700 font-bold text-xs">MDR</div>
                </label>
                
                 <!-- BRI -->
                <label class="flex items-center p-4 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                    <input type="radio" name="payment_method" value="BRI Virtual Account" class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                    <div class="ml-4 flex-1">
                        <span class="block font-bold text-slate-800">BRI Virtual Account</span>
                        <span class="block text-sm text-slate-500">Cek otomatis</span>
                    </div>
                     <div class="w-10 h-10 bg-blue-600/10 rounded-lg flex items-center justify-center text-blue-700 font-bold text-xs">BRI</div>
                </label>
            </div>
            
            <div class="p-6 bg-slate-50 border-t border-slate-100">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-sm font-medium text-slate-600">Total Tagihan</span>
                     <span class="text-xl font-bold text-indigo-700">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</span>
                </div>
                <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-200">
                    Lanjut Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
