@extends('layouts.admin')

@section('title','Buat Tagihan')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Buat Tagihan</h2>

    @if($errors->any())
        <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="/admin/pembayaran" method="POST" class="mt-4 space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-slate-700 mb-1">NRP</label>
            <select name="nrp" id="nrp-select" required class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Mahasiswa --</option>
                @foreach($mahasiswas as $m)
                    <option value="{{ $m->nrp }}">{{ $m->nrp }} — {{ $m->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Jenis</label>
            <input name="jenis" required class="w-full border rounded px-3 py-2" value="Tagihan Semester">
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1 font-medium">Jumlah Tagihan</label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-slate-400 text-sm">Rp</span>
                <input name="jumlah" id="jumlah-input" required type="number" step="0.01" 
                    class="w-full border rounded pl-10 pr-3 py-2 bg-slate-50 font-semibold text-slate-700" readonly
                    placeholder="Pilih mahasiswa untuk menghitung...">
            </div>
            <p id="sks-info" class="text-[11px] text-slate-500 mt-1.5 flex items-center">
                <!-- Info will be injected here -->
            </p>
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Batas Pembayaran</label>
            <input name="batas_pembayaran" type="date" required class="w-full border rounded px-3 py-2 text-sm">
        </div>

        <div class="pt-4">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-semibold transition-colors shadow-sm text-sm">
                Simpan Tagihan
            </button>
            <a href="/admin/pembayaran" class="ml-4 text-sm font-medium text-slate-500 hover:text-slate-700">Batal</a>
        </div>
    </form>
</div>

<script>
document.getElementById('nrp-select').addEventListener('change', function() {
    const nrp = this.value;
    const jumlahInput = document.getElementById('jumlah-input');
    const sksInfo = document.getElementById('sks-info');
    
    if (nrp) {
        sksInfo.innerHTML = '<span class="animate-pulse text-blue-500">⏳ Sedang menghitung SKS...</span>';
        jumlahInput.value = '';

        fetch(`/admin/api/student-amount/${nrp}`)
            .then(response => {
                if(!response.ok) throw new Error('Server Error');
                return response.json();
            })
            .then(data => {
                jumlahInput.value = data.amount;
                if(data.sks > 0) {
                    sksInfo.innerHTML = `
                        <svg class="h-3 w-3 mr-1 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Terdeteksi <strong>${data.sks} SKS</strong> untuk periode <strong>${data.period}</strong>
                    `;
                } else {
                    sksInfo.innerHTML = '<span class="text-orange-500">⚠️ Mahasiswa ini belum mengambil KRS/DKBS semester ini.</span>';
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                sksInfo.innerHTML = '<span class="text-red-500">❌ Gagal mengambil data mahasiswa (Error Database/Server).</span>';
            });
    } else {
        jumlahInput.value = '';
        sksInfo.innerHTML = '';
    }
});
</script>
@endsection
