@extends('layouts.dosen')

@section('title', 'Edit Nilai Mahasiswa')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="bg-amber-600 px-8 py-6 text-white">
            <h2 class="text-2xl font-bold">Edit Nilai Akademik</h2>
            <p class="text-amber-100 text-sm mt-1">Sesuaikan nilai untuk setiap pertemuan (1-16)</p>
        </div>

        <form action="/dosen/nilai/{{ $nilai->id }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Student & Course Information (Non-editable for safety) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-100 opacity-80">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Mahasiswa</label>
                    <input type="text" value="{{ $nilai->nrp }} - {{ $nilai->mahasiswa->nama }}" disabled class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-100 cursor-not-allowed">
                    <input type="hidden" name="nrp" value="{{ $nilai->nrp }}">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Mata Kuliah</label>
                    <input type="text" value="{{ $nilai->kode_mk }} - {{ $nilai->mataKuliah->nama_mk }}" disabled class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-100 cursor-not-allowed">
                    <input type="hidden" name="kode_mk" value="{{ $nilai->kode_mk }}">
                </div>
            </div>

            <!-- Scores Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                <!-- P1-P7 -->
                @for($i=1; $i<=7; $i++)
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Pert. {{ $i }}</label>
                    <input type="number" name="p{{ $i }}" value="{{ old('p'.$i, $nilai->{'p'.$i}) }}" min="0" max="100" step="0.1" placeholder="0" class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 transition text-center font-bold text-slate-700">
                </div>
                @endfor

                <!-- UTS (P8) -->
                <div class="bg-amber-50 rounded-xl p-1 border border-amber-200">
                    <label class="block text-[10px] font-bold text-amber-600 uppercase mb-1 text-center">P8 (UTS)</label>
                    <input type="number" name="uts" value="{{ old('uts', $nilai->uts) }}" min="0" max="100" step="0.1" placeholder="0" class="w-full border border-amber-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 transition text-center font-bold text-amber-700 bg-white">
                </div>

                <!-- P9-P15 -->
                @for($i=9; $i<=15; $i++)
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Pert. {{ $i }}</label>
                    <input type="number" name="p{{ $i }}" value="{{ old('p'.$i, $nilai->{'p'.$i}) }}" min="0" max="100" step="0.1" placeholder="0" class="w-full border border-slate-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 transition text-center font-bold text-slate-700">
                </div>
                @endfor

                <!-- UAS (P16) -->
                <div class="bg-blue-50 rounded-xl p-1 border border-blue-200">
                    <label class="block text-[10px] font-bold text-blue-600 uppercase mb-1 text-center">P16 (UAS)</label>
                    <input type="number" name="uas" value="{{ old('uas', $nilai->uas) }}" min="0" max="100" step="0.1" placeholder="0" class="w-full border border-blue-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 transition text-center font-bold text-blue-700 bg-white">
                </div>
            </div>

            <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100">
                <p class="text-xs text-indigo-700 leading-relaxed">
                    <span class="font-bold">Info Perhitungan (Bobot Baru):</span><br>
                    • <b>Nilai Tugas (Pert. 1-7, 9-15):</b> Bobot 60% (diambil dari rata-rata)<br>
                    • <b>UTS (Pert. 8):</b> Bobot 20%<br>
                    • <b>UAS (Pert. 16):</b> Bobot 20%<br>
                </p>
            </div>

            <div class="flex items-center justify-between bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="text-center px-4 py-2 bg-white rounded-xl border border-slate-200 shadow-sm">
                        <span class="block text-[10px] text-slate-400 font-bold uppercase">Total Skor</span>
                        <span class="text-xl font-bold text-slate-800">{{ $nilai->nilai_total }}</span>
                    </div>
                    <div class="text-center px-6 py-2 bg-indigo-600 rounded-xl shadow-lg shadow-indigo-100">
                        <span class="block text-[10px] text-indigo-100 font-bold uppercase">Predikat</span>
                        <span class="text-xl font-black text-white">{{ $nilai->nilai_akhir }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="/dosen/nilai" class="text-slate-500 font-bold px-6 py-3">Batal</a>
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-10 py-3 rounded-xl shadow-lg transition transform hover:-translate-y-1 active:translate-y-0 text-lg">
                        Update Nilai
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
