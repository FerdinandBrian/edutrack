@extends('layouts.admin')

@section('title', 'Tambah DKBS Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="bg-blue-900 px-8 py-6">
            <div class="flex items-center gap-4">
                <a href="/admin/dkbs" class="text-white/60 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-xl font-bold text-white">Daftar Mahasiswa ke Kelas</h2>
            </div>
        </div>

        <div class="p-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="/admin/dkbs" method="POST" class="space-y-8">
                @csrf

                <!-- Section 1: Mahasiswa -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Informasi Mahasiswa</h3>
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <label class="block text-sm font-medium text-slate-600 mb-2">Pilih Mahasiswa</label>
                        <select name="nrp" id="nrp-select" required class="w-full">
                            <option value="">-- Cari Nama atau NRP Mahasiswa --</option>
                            @foreach($mahasiswas as $m)
                                <option value="{{ $m->nrp }}" {{ (isset($selectedNrp) && $selectedNrp == $m->nrp) ? 'selected' : '' }}>
                                    {{ $m->nrp }} - {{ $m->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Section 2: Pendaftaran Kelas -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Kelas & Jadwal</h3>
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">1. Pilih Tahun Ajaran</label>
                            <select id="ta-select" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition outline-none bg-white">
                                <option value="">-- Pilih Periode --</option>
                                @foreach($periods as $ta)
                                    <option value="{{ $ta }}">{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">2. Pilih Mata Kuliah</label>
                            <select id="matkul-select" disabled class="w-full">
                                <option value="">-- Pilih Tahun Ajaran Dahulu --</option>
                            </select>
                        </div>

                        <div id="kelas-selection-container" class="hidden">
                            <label class="block text-sm font-medium text-slate-600 mb-2">3. Pilih Kelas & Jadwal</label>
                            <div id="kelas-options-list" class="space-y-3">
                                <!-- Dynamic Radio Cards will appear here -->
                            </div>
                            <input type="hidden" name="id_perkuliahan" id="id_perkuliahan_input" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Status Pendaftaran</label>
                            <select name="status" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition outline-none bg-white">
                                <option value="Terdaftar">Terdaftar (Aktif)</option>
                                <option value="Menunggu Antrean">Menunggu Antrean (Waiting List)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-8 flex items-center justify-end gap-4 border-t border-slate-50">
                    <a href="/admin/dkbs" class="px-6 py-3 text-slate-500 hover:text-slate-700 font-medium transition">Batal</a>
                    <button type="submit" id="submit-btn" disabled class="bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 disabled:shadow-none text-white font-bold px-10 py-3 rounded-xl shadow-lg shadow-blue-200 transition transform hover:-translate-y-0.5 active:translate-y-0">
                        Simpan ke DKBS
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
        @apply border-blue-200 bg-blue-50/30;
    }
    .radio-card.selected {
        @apply border-blue-500 bg-blue-50 ring-2 ring-blue-500/10;
    }
</style>

<!-- Scripts for Dynamic Loading -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    const nrpSelect = new TomSelect("#nrp-select", {
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
    const submitBtn = document.getElementById('submit-btn');

    // Store raw perkuliahan data
    let allPerkuliahan = [];

    // Store mahasiswa data with jurusan
    const mahasiswaData = {
        @foreach($mahasiswas as $m)
            '{{ $m->nrp }}': '{{ $m->jurusan }}',
        @endforeach
    };

    let selectedJurusan = @json($selectedJurusan ?? null);

    // Reset logic
    function resetAllDownstream(level) {
        if (level <= 1) { // When NRP changes
            taSelect.value = '';
            selectedJurusan = mahasiswaData[document.getElementById('nrp-select').value];
        }
        if (level <= 2) { // When Yearly TA changes
            matkulSelect.clear();
            matkulSelect.clearOptions();
            matkulSelect.disable();
        }
        if (level <= 3) { // When Matkul changes
            kelasContainer.classList.add('hidden');
            kelasList.innerHTML = '';
            hiddenInput.value = '';
            submitBtn.disabled = true;
        }
    }

    document.getElementById('nrp-select').addEventListener('change', () => resetAllDownstream(1));

    taSelect.addEventListener('change', async function() {
        resetAllDownstream(2);
        const ta = this.value;
        if (!ta || !selectedJurusan) return;

        try {
            const response = await fetch(`/admin/api/perkuliahan-by-ta-jurusan?tahun_ajaran=${encodeURIComponent(ta)}&jurusan=${encodeURIComponent(selectedJurusan)}`);
            allPerkuliahan = await response.json();

            // Extract Unique Matkul
            const matkuls = [...new Set(allPerkuliahan.map(p => p.nama_mk))];
            
            matkuls.forEach(m => {
                matkulSelect.addOption({value: m, text: m});
            });
            
            matkulSelect.enable();
            matkulSelect.focus();
        } catch (e) {
            console.error(e);
        }
    });

    matkulSelect.on('change', function(value) {
        resetAllDownstream(3);
        if (!value) return;

        const filteredClasses = allPerkuliahan.filter(p => p.nama_mk === value);
        
        filteredClasses.forEach(p => {
            const card = document.createElement('div');
            card.className = 'radio-card border-2 border-slate-100 bg-white p-4 rounded-xl transition-all flex items-center justify-between cursor-pointer hover:border-blue-200 hover:bg-blue-50/30';
            card.innerHTML = `
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-lg class-badge">
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
                <div class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center bg-white transition-all radio-check">
                    <div class="w-3 h-3 rounded-full bg-blue-600 scale-0 transition-all"></div>
                </div>
            `;
            
            card.onclick = () => selectClass(card, p.id);
            kelasList.appendChild(card);
        });

        kelasContainer.classList.remove('hidden');
    });

    function selectClass(target, id) {
        document.querySelectorAll('.radio-card').forEach(c => {
            c.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500/10');
            c.querySelector('.radio-check').classList.remove('border-blue-500');
            c.querySelector('.radio-check div').classList.remove('scale-100');
        });
        
        target.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500/10');
        target.querySelector('.radio-check').classList.add('border-blue-500');
        target.querySelector('.radio-check div').classList.add('scale-100');
        
        hiddenInput.value = id;
        submitBtn.disabled = false;
    }
</script>
@endsection