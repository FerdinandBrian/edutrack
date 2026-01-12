@extends('layouts.admin')

@section('title', 'Buka Kelas Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="bg-indigo-900 px-8 py-6">
            <div class="flex items-center gap-4">
                <a href="/admin/perkuliahan" class="text-white/60 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-xl font-bold text-white">Buka Kelas Perkuliahan Baru</h2>
            </div>
        </div>

        <div class="p-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <p>{{ $errors->first('msg') ?: $errors->first() }}</p>
                </div>
            @endif

            <form action="/admin/perkuliahan" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Prodi Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 mb-2">Pilih Program Studi</label>
                        <select id="prodiSelect" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition outline-none bg-slate-50">
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mata Kuliah -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 mb-2">Pilih Mata Kuliah (Master Data)</label>
                        <select name="kode_mk" id="mkSelect" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition outline-none bg-slate-50">
                            <option value="">-- Pilih Program Studi Terlebih Dahulu --</option>
                        </select>
                    </div>

                    <!-- Dosen Pengampu -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Dosen Pengampu</label>
                        <select name="nip_dosen" id="dosenSelect" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition outline-none bg-slate-50">
                            <option value="">-- Pilih Program Studi Terlebih Dahulu --</option>
                        </select>
                    </div>

                    <!-- Kelas -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Nama Kelas</label>
                        <input type="text" name="kelas" required placeholder="Contoh: A, B, atau IF-44-01" value="{{ old('kelas') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition outline-none bg-slate-50">
                    </div>

                    <!-- Hari -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Hari</label>
                        <select name="hari" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition outline-none bg-slate-50">
                            <option value="">-- Pilih Hari --</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $h)
                                <option value="{{ $h }}" {{ old('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ruangan -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Ruangan</label>
                        <input type="text" name="kode_ruangan" id="ruanganInput" list="ruanganList" required placeholder="Ketik Kode Ruangan (Contoh: L8001)" value="{{ old('kode_ruangan') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition outline-none bg-slate-50 uppercase">
                        <datalist id="ruanganList">
                            @foreach($ruangans as $r)
                                <option value="{{ $r->kode_ruangan }}">{{ $r->nama_ruangan }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Tahun Ajaran -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Tahun Ajaran</label>
                        <select name="tahun_ajaran" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition outline-none bg-slate-50">
                            <option value="">-- Pilih Periode --</option>
                            @foreach(['2024/2025 - Ganjil', '2024/2025 - Genap', '2025/2026 - Ganjil', '2025/2026 - Genap', '2026/2027 - Ganjil', '2026/2027 - Genap', '2027/2028 - Ganjil', '2027/2028 - Genap'] as $ta)
                                <option value="{{ $ta }}" {{ old('tahun_ajaran') == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jam Mulai -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Jam Mulai</label>
                        <input type="time" name="jam_mulai" required value="{{ old('jam_mulai') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition outline-none bg-slate-50">
                    </div>

                    <!-- Jam Berakhir -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Jam Berakhir (Otomatis)</label>
                        <input type="time" name="jam_berakhir" id="jamBerakhir" readonly required value="{{ old('jam_berakhir') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 transition outline-none bg-slate-100 cursor-not-allowed font-semibold text-indigo-700" title="Jam berakhir dihitung otomatis berdasarkan SKS atau jenis kelas">
                    </div>
                </div>

                <div class="pt-8 flex items-center justify-end gap-4 border-t border-slate-50">
                    <a href="/admin/perkuliahan" class="px-6 py-3 text-slate-500 hover:text-slate-700 font-medium transition">Batal</a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-10 py-3 rounded-xl shadow-lg shadow-indigo-200 transition transform hover:-translate-y-0.5 active:translate-y-0">
                        Buka Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const prodiSelect = document.getElementById('prodiSelect');
        const dosenSelect = document.getElementById('dosenSelect');
        const mkSelect = document.getElementById('mkSelect');

        prodiSelect.addEventListener('change', function() {
            const jurusan = this.value;
            
            dosenSelect.innerHTML = '<option value="">Sedang memuat...</option>';
            mkSelect.innerHTML = '<option value="">Sedang memuat...</option>';
            
            if (!jurusan) {
                 dosenSelect.innerHTML = '<option value="">-- Pilih Program Studi Terlebih Dahulu --</option>';
                 mkSelect.innerHTML = '<option value="">-- Pilih Program Studi Terlebih Dahulu --</option>';
                 return;
            }

            // Fetch Dosen
            fetch(`/admin/api/dosen-by-jurusan?jurusan=${encodeURIComponent(jurusan)}`)
                .then(response => response.json())
                .then(data => {
                    dosenSelect.innerHTML = '<option value="">-- Pilih Dosen --</option>';
                    if(data.length === 0) {
                        dosenSelect.innerHTML += '<option value="" disabled>Tidak ada dosen untuk prodi ini</option>';
                    }
                    data.forEach(dosen => {
                        dosenSelect.innerHTML += `<option value="${dosen.nip}">${dosen.nama} (${dosen.nip})</option>`;
                    });
                })
                .catch(err => {
                    console.error('Error fetching dosens:', err);
                    dosenSelect.innerHTML = '<option value="">Gagal memuat dosen</option>';
                });

            // Fetch Mata Kuliah (via Stored Procedure API)
            fetch(`/admin/api/mata-kuliah-by-jurusan?jurusan=${encodeURIComponent(jurusan)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('API Error:', data.error);
                        mkSelect.innerHTML = `<option value="">Error: ${data.error}</option>`;
                        return;
                    }

                    mkSelect.innerHTML = '<option value="">-- Pilih Mata Kuliah --</option>';
                    if(data.length === 0) {
                        mkSelect.innerHTML += '<option value="" disabled>Tidak ada mata kuliah untuk prodi ini</option>';
                    }
                    data.forEach(mk => {
                        mkSelect.innerHTML += `<option value="${mk.kode_mk}">[${mk.kode_mk}] ${mk.nama_mk} (${mk.sks} SKS) - Smst ${mk.semester}</option>`;
                    });
                })
                .catch(err => {
                    console.error('Error fetching matkul:', err);
                    mkSelect.innerHTML = '<option value="">Gagal memuat mata kuliah (Cek Console)</option>';
                });
        });

        // Auto-calculate End Time
        const jamMulaiInput = document.querySelector('input[name="jam_mulai"]');
        const jamBerakhirInput = document.getElementById('jamBerakhir');

        function updateEndTime() {
            const mkOption = mkSelect.options[mkSelect.selectedIndex];
            const startTime = jamMulaiInput.value;
            
            if (!mkOption || !mkOption.value || !startTime) {
                // Don't clear if there's an old value, but usually it's better to clear if invalid
                return;
            }

            const mkText = mkOption.text.toLowerCase();
            const mkKode = mkOption.value;
            let minutes = 0;

            // Same logic as controller: Praktikum = 120 mins, others = SKS * 50
            // Check name for 'praktikum' or kode_mk for 'P' suffix
            if (mkText.includes('praktikum') || mkKode.endsWith('P')) {
                minutes = 120;
            } else {
                const sksMatch = mkOption.text.match(/\((\d+)\s*SKS\)/);
                const sks = sksMatch ? parseInt(sksMatch[1]) : 2; // Default 2 SKS
                minutes = sks * 50;
            }

            const [hours, mins] = startTime.split(':').map(Number);
            const date = new Date();
            date.setHours(hours, mins + minutes, 0);
            
            const endHours = String(date.getHours()).padStart(2, '0');
            const endMins = String(date.getMinutes()).padStart(2, '0');
            
            jamBerakhirInput.value = `${endHours}:${endMins}`;
        }

        mkSelect.addEventListener('change', updateEndTime);
        jamMulaiInput.addEventListener('change', updateEndTime);
    });
</script>
