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
                            <label class="block text-sm font-medium text-slate-600 mb-2">1. Pilih Tahun Ajaran</label>
                            <select id="ta-select" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-white">
                                <option value="">-- Pilih Periode --</option>
                                @foreach($periods as $ta)
                                    <option value="{{ $ta }}" {{ $dkbs->tahun_ajaran == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">2. Pilih Mata Kuliah</label>
                            <select id="matkul-select" required class="w-full">
                                <option value="">-- Pilih Tahun Ajaran Dahulu --</option>
                            </select>
                        </div>

                        <div id="kelas-selection-container" class="hidden">
                            <label class="block text-sm font-medium text-slate-600 mb-2">3. Pilih Kelas & Jadwal</label>
                            <div id="kelas-options-list" class="space-y-3">
                                <!-- Dynamic Radio Cards -->
                            </div>
                            <input type="hidden" name="id_perkuliahan" id="id_perkuliahan_input" value="{{ $dkbs->id_perkuliahan }}" required>
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
                    <button type="submit" id="submit-btn" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-10 py-3 rounded-xl shadow-lg shadow-amber-200 transition transform hover:-translate-y-0.5 active:translate-y-0">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .radio-card {
        @apply cursor-pointer border-2 border-slate-100 bg-white p-4 rounded-xl transition-all flex items-center justify-between;
    }
    .radio-card:hover {
        @apply border-amber-200 bg-amber-50/30;
    }
    .radio-card.selected {
        @apply border-amber-500 bg-amber-50 ring-2 ring-amber-500/10;
    }
</style>

<!-- Scripts for Dynamic Loading -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect("#nrp-select", {
        create: false,
        sortField: { field: "text", direction: "asc" }
    });

    const matkulSelect = new TomSelect("#matkul-select", {
        create: false,
        placeholder: "Cari Mata Kuliah...",
        sortField: { field: "text", direction: "asc" }
    });

    const taSelect = document.getElementById('ta-select');
    const kelasContainer = document.getElementById('kelas-selection-container');
    const kelasList = document.getElementById('kelas-options-list');
    const hiddenInput = document.getElementById('id_perkuliahan_input');
    const currentIdPerkuliahan = "{{ $dkbs->id_perkuliahan }}";

    let allPerkuliahan = [];

    async function updatePerkuliahanOptions(ta, initialLoad = false) {
        matkulSelect.clear();
        matkulSelect.clearOptions();
        matkulSelect.disable();
        kelasContainer.classList.add('hidden');
        kelasList.innerHTML = '';

        if (!ta) return;

        try {
            const response = await fetch(`/admin/api/perkuliahan-by-ta?tahun_ajaran=${encodeURIComponent(ta)}`);
            allPerkuliahan = await response.json();

            const matkuls = [...new Set(allPerkuliahan.map(p => p.nama_mk))];
            matkuls.forEach(m => matkulSelect.addOption({value: m, text: m}));
            matkulSelect.enable();

            if (initialLoad) {
                const currentMatkul = allPerkuliahan.find(p => p.id == currentIdPerkuliahan)?.nama_mk;
                if (currentMatkul) {
                    matkulSelect.setValue(currentMatkul);
                    renderClasses(currentMatkul, currentIdPerkuliahan);
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    function renderClasses(matkulName, selectedId = null) {
        kelasList.innerHTML = '';
        const filtered = allPerkuliahan.filter(p => p.nama_mk === matkulName);
        
        filtered.forEach(p => {
            const isSelected = selectedId && p.id == selectedId;
            const card = document.createElement('div');
            card.className = `radio-card border-2 border-slate-100 bg-white p-4 rounded-xl transition-all flex items-center justify-between cursor-pointer hover:border-amber-200 hover:bg-amber-50/30 ${isSelected ? 'border-amber-500 bg-amber-50 ring-2 ring-amber-500/10' : ''}`;
            
            card.innerHTML = `
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full ${isSelected ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500'} flex items-center justify-center font-bold text-lg class-badge">
                        ${p.kelas}
                    </div>
                    <div>
                        <div class="font-bold text-slate-800">Kelas ${p.kelas}</div>
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            ${p.hari}, ${p.jam_mulai} - ${p.jam_berakhir}
                        </div>
                    </div>
                </div>
                <div class="w-6 h-6 rounded-full border-2 ${isSelected ? 'border-amber-500' : 'border-slate-200'} flex items-center justify-center bg-white transition-all radio-check">
                    <div class="w-3 h-3 rounded-full bg-amber-600 ${isSelected ? 'scale-100' : 'scale-0'} transition-all"></div>
                </div>
            `;
            
            card.onclick = () => {
                document.querySelectorAll('.radio-card').forEach(c => {
                    c.classList.remove('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/10');
                    c.querySelector('.radio-check').classList.remove('border-amber-500');
                    c.querySelector('.radio-check div').classList.remove('scale-100');
                    c.querySelector('.class-badge').classList.replace('bg-amber-100', 'bg-slate-100');
                    c.querySelector('.class-badge').classList.replace('text-amber-700', 'text-slate-500');
                });
                card.classList.add('border-amber-500', 'bg-amber-50', 'ring-2', 'ring-amber-500/10');
                card.querySelector('.radio-check').classList.add('border-amber-500');
                card.querySelector('.radio-check div').classList.add('scale-100');
                card.querySelector('.class-badge').classList.replace('bg-slate-100', 'bg-amber-100');
                card.querySelector('.class-badge').classList.replace('text-slate-500', 'text-amber-700');
                hiddenInput.value = p.id;
            };
            kelasList.appendChild(card);
        });
        kelasContainer.classList.remove('hidden');
    }

    taSelect.addEventListener('change', function() {
        updatePerkuliahanOptions(this.value);
    });

    matkulSelect.on('change', function(value) {
        if (value) renderClasses(value);
    });

    window.addEventListener('DOMContentLoaded', () => {
        if (taSelect.value) {
            updatePerkuliahanOptions(taSelect.value, true);
        }
    });
</script>
@endsection