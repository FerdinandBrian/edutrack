@extends('layouts.dosen')

@section('title', 'Tambah Nilai Mahasiswa')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="bg-emerald-600 px-8 py-6 text-white">
            <h2 class="text-2xl font-bold">Input Nilai Akademik</h2>
            <p class="text-emerald-100 text-sm mt-1">Isi nilai untuk setiap pertemuan (1-16)</p>
        </div>

        <form action="/dosen/nilai" method="POST" class="p-8 space-y-8">
            @csrf
            
            <!-- Student & Course Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Mahasiswa</label>
                    <select name="nrp" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 transition outline-none">
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach($mahasiswas as $m)
                            <option value="{{ $m->nrp }}">{{ $m->nrp }} - {{ $m->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Mata Kuliah</label>
                    <select name="kode_mk" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 transition outline-none">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($mataKuliahs as $mk)
                            <option value="{{ $mk->kode_mk }}">{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Scores Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                <!-- P1-P7 -->
                @for($i=1; $i<=7; $i++)
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Pert. {{ $i }}</label>
                    <input type="number" name="p{{ $i }}" min="0" max="100" step="0.1" placeholder="0" class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 transition text-center font-bold text-slate-700">
                </div>
                @endfor

                <!-- UTS (P8) -->
                <div class="bg-amber-50 rounded-xl p-1 border border-amber-200">
                    <label class="block text-[10px] font-bold text-amber-600 uppercase mb-1 text-center">P8 (UTS)</label>
                    <input type="number" name="uts" min="0" max="100" step="0.1" placeholder="0" class="w-full border border-amber-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 transition text-center font-bold text-amber-700 bg-white">
                </div>

                <!-- P9-P15 -->
                @for($i=9; $i<=15; $i++)
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Pert. {{ $i }}</label>
                    <input type="number" name="p{{ $i }}" min="0" max="100" step="0.1" placeholder="0" class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 transition text-center font-bold text-slate-700">
                </div>
                @endfor

                <!-- UAS (P16) -->
                <div class="bg-blue-50 rounded-xl p-1 border border-blue-200">
                    <label class="block text-[10px] font-bold text-blue-600 uppercase mb-1 text-center">P16 (UAS)</label>
                    <input type="number" name="uas" min="0" max="100" step="0.1" placeholder="0" class="w-full border border-blue-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition text-center font-bold text-blue-700 bg-white">
                </div>
            </div>

            <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100">
                <p class="text-xs text-indigo-700 leading-relaxed">
                    <span class="font-bold">Info Perhitungan:</span><br>
                    • <b>Nilai Tugas (Pert. 1-7, 9-15):</b> Bobot 60% (diambil dari rata-rata)<br>
                    • <b>UTS (Pert. 8):</b> Bobot 20%<br>
                    • <b>UAS (Pert. 16):</b> Bobot 20%<br>
                    <i>* Nilai Akhir (Grade) akan otomatis dihitung saat Anda menekan Simpan.</i>
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="/dosen/nilai" class="text-slate-500 font-bold px-6 py-3">Batal</a>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-10 py-3 rounded-xl shadow-lg transition transform hover:-translate-y-1 active:translate-y-0 text-lg">
                    Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
