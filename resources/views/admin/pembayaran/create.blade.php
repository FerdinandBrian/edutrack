@extends('layouts.admin')

@section('title', 'Buat Tagihan Baru')

@section('content')
<div class="mb-6">
    <a href="{{ isset($selectedNrp) ? '/admin/pembayaran/student/'.$selectedNrp : '/admin/pembayaran' }}" class="text-slate-500 hover:text-indigo-600 transition-colors flex items-center gap-2 text-sm font-medium group">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
        <h2 class="text-lg font-bold text-slate-800">Buat Tagihan Baru</h2>
        <p class="text-xs text-slate-500 mt-1">Isi detail di bawah untuk membuat invoice pembayaran mahasiswa.</p>
    </div>

    @if($errors->any())
        <div class="mx-6 mt-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="/admin/pembayaran" method="POST" class="p-6 space-y-6">
        @csrf
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Pilih Mahasiswa</label>
            <select name="nrp" id="nrp-select" required 
                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                <option value="">-- Cari atau Pilih Mahasiswa --</option>
                @foreach($mahasiswas as $m)
                    <option value="{{ $m->nrp }}" {{ (isset($selectedNrp) && $selectedNrp == $m->nrp) ? 'selected' : '' }}>
                        {{ $m->nrp }} — {{ $m->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Jenis Tagihan</label>
            <div class="relative group">
                <input name="jenis" required list="jenis-options" 
                       class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" 
                       value="Tagihan Semester">
                <datalist id="jenis-options">
                    <option value="Tagihan Semester">
                    <option value="Biaya Lab/Praktikum">
                    <option value="Biaya Sidang/Wisuda">
                    <option value="Denda Keterlambatan">
                </datalist>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Jumlah Tagihan (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-2.5 text-slate-400 text-sm font-mono leading-none py-0.5">Rp</span>
                    <input name="jumlah" id="jumlah-input" required type="number" step="1" 
                        class="w-full border border-slate-200 rounded-xl pl-12 pr-4 py-2.5 bg-slate-50 font-bold text-slate-700 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" 
                        placeholder="0">
                </div>
                <p id="sks-info" class="mt-2 flex items-center"></p>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Batas Pembayaran (Deadline)</label>
                <input name="batas_pembayaran" type="date" required 
                       class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-50 flex items-center gap-4">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-indigo-100 flex items-center gap-2 group active:scale-95">
                Buat Tagihan
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
            <a href="{{ isset($selectedNrp) ? '/admin/pembayaran/student/'.$selectedNrp : '/admin/pembayaran' }}" class="text-sm font-bold text-slate-400 hover:text-rose-500 transition-colors">
                Batalkan
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('nrp-select').addEventListener('change', function() {
    const nrp = this.value;
    const jumlahInput = document.getElementById('jumlah-input');
    const sksInfo = document.getElementById('sks-info');
    
    if (nrp) {
        sksInfo.innerHTML = '<span class="flex items-center gap-2 text-[10px] font-bold text-indigo-400 uppercase tracking-widest animate-pulse"><div class="w-1.5 h-1.5 bg-indigo-400 rounded-full"></div> Menghitung SKS...</span>';
        
        // Remove readOnly temporarily to show placeholder effect
        jumlahInput.classList.add('opacity-50');

        fetch(`/admin/api/student-amount/${nrp}`)
            .then(response => {
                if(!response.ok) throw new Error('Server Error');
                return response.json();
            })
            .then(data => {
                jumlahInput.classList.remove('opacity-50');
                jumlahInput.value = Math.floor(data.amount);
                
                if(data.sks > 0) {
                    sksInfo.innerHTML = `
                        <div class="bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg border border-emerald-100 flex items-center gap-2">
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-[11px] font-semibold">Terdeteksi <b>${data.sks} SKS</b> (${data.period})</span>
                        </div>
                    `;
                } else {
                    sksInfo.innerHTML = `
                        <div class="bg-amber-50 text-amber-700 px-3 py-1.5 rounded-lg border border-amber-100 flex items-center gap-2">
                             <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                             <span class="text-[11px] font-semibold text-amber-600">No KRS found for this period</span>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                jumlahInput.classList.remove('opacity-50');
                sksInfo.innerHTML = '<span class="text-xs font-bold text-rose-500 flex items-center gap-1">⚠️ Gagal mengambil data kalkulasi</span>';
            });
    } else {
        jumlahInput.value = '';
        sksInfo.innerHTML = '';
    }
});

// Trigger change on load if NRP is pre-selected
if (document.getElementById('nrp-select').value) {
    document.getElementById('nrp-select').dispatchEvent(new Event('change'));
}
</script>
@endsection
