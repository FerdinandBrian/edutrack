@extends('layouts.admin')

@section('title', 'Edit Tagihan')

@section('content')
<div class="mb-6">
    <a href="/admin/pembayaran/student/{{ $tagihan->nrp }}" class="text-slate-500 hover:text-indigo-600 transition-colors flex items-center gap-2 text-sm font-medium group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Pembayaran Mahasiswa
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
        <h2 class="text-lg font-bold text-slate-800">Edit Detail Tagihan</h2>
        <p class="text-xs text-slate-500 mt-1">Mengubah data tagihan untuk <strong>{{ $tagihan->mahasiswa->nama }}</strong></p>
    </div>

    @if($errors->any())
        <div class="mx-6 mt-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="/admin/pembayaran/{{ $tagihan->id }}" method="POST" class="p-6 space-y-5">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Mahasiswa (NRP)</label>
            <select name="nrp" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                @foreach($mahasiswas as $m)
                    <option value="{{ $m->nrp }}" {{ $m->nrp == $tagihan->nrp ? 'selected' : '' }}>{{ $m->nrp }} — {{ $m->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Jenis Tagihan</label>
            <input name="jenis" value="{{ $tagihan->jenis }}" required 
                   class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Jumlah (Rp)</label>
                <input name="jumlah" value="{{ (int)$tagihan->jumlah }}" required type="number" 
                       class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Batas Pembayaran</label>
                <input name="batas_pembayaran" value="{{ $tagihan->batas_pembayaran }}" type="date" required 
                       class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Status Pembayaran</label>
            <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-200 group">
                    <input type="radio" name="status" value="Belum Lunas" {{ $tagihan->status != 'Lunas' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-3 text-sm font-bold text-slate-700 group-has-[:checked]:text-indigo-700">Belum Lunas</span>
                </label>
                <label class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-200 group">
                    <input type="radio" name="status" value="Lunas" {{ $tagihan->status == 'Lunas' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                    <span class="ml-3 text-sm font-bold text-slate-700 group-has-[:checked]:text-emerald-700">Sudah Lunas</span>
                </label>
            </div>
        </div>

        <div class="pt-4 flex items-center gap-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-indigo-100 active:scale-95">
                Simpan Perubahan
            </button>
            <a href="/admin/pembayaran/student/{{ $tagihan->nrp }}" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection