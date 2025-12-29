@extends('layouts.admin')

@section('title', 'Edit DKBS')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="bg-amber-600 px-8 py-6">
            <div class="flex items-center gap-4">
                <a href="/admin/dkbs" class="text-white/60 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-xl font-bold text-white">Edit Pendaftaran Kelas (DKBS)</h2>
            </div>
        </div>

        <div class="p-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="/admin/dkbs/{{ $dkbs->id }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Section 1: Mahasiswa -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-8 h-px bg-slate-200"></span>
                        Mahasiswa
                    </h3>
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <label class="block text-sm font-medium text-slate-600 mb-2">Nama atau NRP Mahasiswa</label>
                        <select name="nrp" id="nrp-select" required class="w-full">
                            @foreach($mahasiswas as $m)
                                <option value="{{ $m->nrp }}" {{ $dkbs->nrp == $m->nrp ? 'selected' : '' }}>
                                    {{ $m->nrp }} - {{ $m->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Section 2: Kelas & Jadwal -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-8 h-px bg-slate-200"></span>
                        Perkuliahan & Jadwal
                    </h3>
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Tahun Ajaran</label>
                            <select id="ta-select" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-white">
                                <option value="">-- Pilih Periode --</option>
                                @foreach(['2024/2025 - Ganjil', '2024/2025 - Genap', '2025/2026 - Ganjil', '2025/2026 - Genap', '2026/2027 - Ganjil', '2026/2027 - Genap', '2027/2028 - Ganjil', '2027/2028 - Genap'] as $ta)
                                    <option value="{{ $ta }}" {{ $dkbs->tahun_ajaran == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Pilih Kelas Perkuliahan</label>
                            <select name="id_perkuliahan" id="perkuliahan-select" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-white">
                                <option value="">-- Pilih Tahun Ajaran Dahulu --</option>
                            </select>
                            <p class="text-[10px] text-slate-400 mt-2 italic font-medium">* Menampilkan daftar kelas yang aktif pada periode terpilih.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Status Pendaftaran</label>
                            <div class="flex gap-6 py-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="Terdaftar" {{ $dkbs->status == 'Terdaftar' ? 'checked' : '' }} class="w-4 h-4 text-blue-600">
                                    <span class="text-sm font-medium text-slate-700">Terdaftar</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="Menunggu Antrean" {{ $dkbs->status == 'Menunggu Antrean' ? 'checked' : '' }} class="w-4 h-4 text-amber-600">
                                    <span class="text-sm font-medium text-slate-700">Antrean</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="Drop" {{ $dkbs->status == 'Drop' ? 'checked' : '' }} class="w-4 h-4 text-red-600">
                                    <span class="text-sm font-medium text-slate-700">Drop</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-8 flex items-center justify-end gap-4 border-t border-slate-50">
                    <a href="/admin/dkbs" class="px-6 py-3 text-slate-500 hover:text-slate-700 font-medium transition">Batal</a>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-10 py-3 rounded-xl shadow-lg shadow-amber-200 transition transform hover:-translate-y-0.5 active:translate-y-0">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts for Dynamic Loading -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect("#nrp-select", {
        create: false,
        sortField: { field: "text", direction: "asc" }
    });

    const taSelect = document.getElementById('ta-select');
    const perkuliahanSelect = document.getElementById('perkuliahan-select');
    const currentIdPerkuliahan = "{{ $dkbs->id_perkuliahan }}";

    async function updatePerkuliahanOptions(ta, selectedId = null) {
        if (!ta) {
            perkuliahanSelect.innerHTML = '<option value="">-- Pilih Tahun Ajaran Dahulu --</option>';
            perkuliahanSelect.disabled = true;
            perkuliahanSelect.classList.add('opacity-50');
            return;
        }

        perkuliahanSelect.innerHTML = '<option value="">-- Memuat Kelas... --</option>';
        perkuliahanSelect.disabled = true;

        try {
            const response = await fetch(`/admin/api/perkuliahan-by-ta?tahun_ajaran=${encodeURIComponent(ta)}`);
            const data = await response.json();

            let options = '<option value="">-- Pilih Kelas --</option>';
            data.forEach(p => {
                const isSelected = selectedId && p.id == selectedId ? 'selected' : '';
                options += `<option value="${p.id}" ${isSelected}>${p.label}</option>`;
            });

            perkuliahanSelect.innerHTML = options;
            perkuliahanSelect.disabled = false;
            perkuliahanSelect.classList.remove('opacity-50');
        } catch (error) {
            console.error('Error fetching perkuliahan:', error);
            perkuliahanSelect.innerHTML = '<option value="">Error memuat data</option>';
        }
    }

    taSelect.addEventListener('change', function() {
        updatePerkuliahanOptions(this.value);
    });

    // Initial load for edit page
    window.addEventListener('DOMContentLoaded', () => {
        if (taSelect.value) {
            updatePerkuliahanOptions(taSelect.value, currentIdPerkuliahan);
        }
    });
</script>
@endsection