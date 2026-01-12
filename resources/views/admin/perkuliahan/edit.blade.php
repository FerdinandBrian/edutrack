@extends('layouts.admin')

@section('title', 'Edit Jadwal Kelas')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="bg-amber-600 px-8 py-6">
            <div class="flex items-center gap-4">
                <a href="/admin/perkuliahan" class="text-white/60 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-xl font-bold text-white">Edit Jadwal & Detail Kelas</h2>
            </div>
        </div>

        <div class="p-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <p>{{ $errors->first('msg') ?: $errors->first() }}</p>
                </div>
            @endif

            <form action="/admin/perkuliahan/{{ $perkuliahan->id_perkuliahan }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Mata Kuliah (Disabled) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 mb-2">Mata Kuliah</label>
                        <select disabled id="mkSelect" data-nama="{{ $perkuliahan->mataKuliah->nama_mk }}" data-sks="{{ $perkuliahan->mataKuliah->sks }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 bg-slate-100 text-slate-500 cursor-not-allowed">
                            <option>[{{ $perkuliahan->kode_mk }}] {{ $perkuliahan->mataKuliah->nama_mk }} ({{ $perkuliahan->mataKuliah->sks }} SKS)</option>
                        </select>
                        <input type="hidden" name="kode_mk" value="{{ $perkuliahan->kode_mk }}">
                    </div>

                    <!-- Dosen Pengampu -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Dosen Pengampu</label>
                        <select name="nip_dosen" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-slate-50">
                            @foreach($dosens as $d)
                                <option value="{{ $d->nip }}" {{ (old('nip_dosen', $perkuliahan->nip_dosen)) == $d->nip ? 'selected' : '' }}>
                                    {{ $d->nama }} ({{ $d->nip }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kelas -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Nama Kelas</label>
                        <input type="text" name="kelas" required value="{{ old('kelas', $perkuliahan->kelas) }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-slate-50">
                    </div>

                    <!-- Hari -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Hari</label>
                        <select name="hari" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-slate-50">
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $h)
                                <option value="{{ $h }}" {{ (old('hari', $perkuliahan->hari)) == $h ? 'selected' : '' }}>{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ruangan -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Ruangan</label>
                        <input type="text" name="kode_ruangan" id="ruanganInput" list="ruanganList" required placeholder="Ketik Kode Ruangan" value="{{ old('kode_ruangan', $perkuliahan->kode_ruangan) }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-slate-50 uppercase">
                        <datalist id="ruanganList">
                            @foreach($ruangans as $r)
                                <option value="{{ $r->kode_ruangan }}">{{ $r->nama_ruangan }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Tahun Ajaran -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Tahun Ajaran</label>
                        <select name="tahun_ajaran" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-slate-50">
                            @foreach(['2024/2025 - Ganjil', '2024/2025 - Genap', '2025/2026 - Ganjil', '2025/2026 - Genap', '2026/2027 - Ganjil', '2026/2027 - Genap', '2027/2028 - Ganjil', '2027/2028 - Genap'] as $ta)
                                <option value="{{ $ta }}" {{ (old('tahun_ajaran', $perkuliahan->tahun_ajaran)) == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jam Mulai -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Jam Mulai</label>
                        <input type="time" name="jam_mulai" required value="{{ old('jam_mulai', \Illuminate\Support\Str::substr($perkuliahan->jam_mulai, 0, 5)) }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-slate-50">
                    </div>

                    <!-- Jam Berakhir -->
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Jam Berakhir (Otomatis)</label>
                        <input type="time" name="jam_berakhir" id="jamBerakhir" readonly required value="{{ old('jam_berakhir', \Illuminate\Support\Str::substr($perkuliahan->jam_berakhir, 0, 5)) }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-slate-100 cursor-not-allowed font-semibold text-amber-700" title="Jam berakhir dihitung otomatis">
                    </div>
                </div>

                <div class="pt-8 flex items-center justify-end gap-4 border-t border-slate-50">
                    <a href="/admin/perkuliahan" class="px-6 py-3 text-slate-500 hover:text-slate-700 font-medium transition">Batal</a>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-10 py-3 rounded-xl shadow-lg shadow-amber-200 transition transform hover:-translate-y-0.5 active:translate-y-0">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mkSelect = document.getElementById('mkSelect');
        const jamMulaiInput = document.querySelector('input[name="jam_mulai"]');
        const jamBerakhirInput = document.getElementById('jamBerakhir');

        function updateEndTime() {
            const mkNama = mkSelect.dataset.nama.toLowerCase();
            const mkKode = document.querySelector('input[name="kode_mk"]').value;
            const mkSks = parseInt(mkSelect.dataset.sks);
            const startTime = jamMulaiInput.value;
            
            if (!startTime) return;

            let minutes = 0;
            // Same logic: 120 for Praktikum, SKS * 50 for others
            if (mkNama.includes('praktikum') || mkKode.endsWith('P')) {
                minutes = 120;
            } else {
                minutes = mkSks * 50;
            }

            const [hours, mins] = startTime.split(':').map(Number);
            const date = new Date();
            date.setHours(hours, mins + minutes, 0);
            
            const endHours = String(date.getHours()).padStart(2, '0');
            const endMins = String(date.getMinutes()).padStart(2, '0');
            
            jamBerakhirInput.value = `${endHours}:${endMins}`;
        }

        jamMulaiInput.addEventListener('change', updateEndTime);
    });
</script>
