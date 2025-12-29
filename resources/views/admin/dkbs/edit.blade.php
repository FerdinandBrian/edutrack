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
                <h2 class="text-xl font-bold text-white">Edit Rencana Studi (DKBS)</h2>
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

                <!-- Section 1: Mahasiswa & Tahun Ajaran -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-slate-800 font-bold border-b border-slate-100 pb-2">
                        <span class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                        <h3>Informasi Dasar</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-600 mb-1">Cari Nama atau NRP Mahasiswa</label>
                            <select name="nrp" id="select-mahasiswa" required placeholder="Masukkan Nama atau NRP...">
                                <option value="">-- Pilih Mahasiswa --</option>
                                @foreach($mahasiswas as $m)
                                    <option value="{{ $m->nrp }}" {{ (old('nrp', $dkbs->nrp)) == $m->nrp ? 'selected' : '' }}>
                                        [{{ $m->nrp }}] {{ $m->nama }} - {{ $m->jurusan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Tahun Ajaran</label>
                            <select name="tahun_ajaran" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition outline-none bg-slate-50">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <option value="2024/2025 - Ganjil" {{ (old('tahun_ajaran', $dkbs->tahun_ajaran)) == '2024/2025 - Ganjil' ? 'selected' : '' }}>2024/2025 - Ganjil</option>
                                <option value="2024/2025 - Genap" {{ (old('tahun_ajaran', $dkbs->tahun_ajaran)) == '2024/2025 - Genap' ? 'selected' : '' }}>2024/2025 - Genap</option>
                                <option value="2025/2026 - Ganjil" {{ (old('tahun_ajaran', $dkbs->tahun_ajaran)) == '2025/2026 - Ganjil' ? 'selected' : '' }}>2025/2026 - Ganjil</option>
                                <option value="2025/2026 - Genap" {{ (old('tahun_ajaran', $dkbs->tahun_ajaran)) == '2025/2026 - Genap' ? 'selected' : '' }}>2025/2026 - Genap</option>
                                <option value="2026/2027 - Ganjil" {{ (old('tahun_ajaran', $dkbs->tahun_ajaran)) == '2026/2027 - Ganjil' ? 'selected' : '' }}>2026/2027 - Ganjil</option>
                                <option value="2026/2027 - Genap" {{ (old('tahun_ajaran', $dkbs->tahun_ajaran)) == '2026/2027 - Genap' ? 'selected' : '' }}>2026/2027 - Genap</option>
                                <option value="2027/2028 - Ganjil" {{ (old('tahun_ajaran', $dkbs->tahun_ajaran)) == '2027/2028 - Ganjil' ? 'selected' : '' }}>2027/2028 - Ganjil</option>
                                <option value="2027/2028 - Genap" {{ (old('tahun_ajaran', $dkbs->tahun_ajaran)) == '2027/2028 - Genap' ? 'selected' : '' }}>2027/2028 - Genap</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Semester</label>
                            <select name="semester" id="select-semester" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition outline-none bg-slate-50">
                                <option value="">-- Pilih Semester --</option>
                                @for($i=1; $i<=8; $i++)
                                    <option value="{{ $i }}" {{ (old('semester', $dkbs->semester)) == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Mata Kuliah -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-slate-800 font-bold border-b border-slate-100 pb-2">
                        <span class="bg-purple-100 text-purple-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                        <h3>Pilih Mata Kuliah</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Mata Kuliah</label>
                            <select name="kode_mk" id="select-mk" required disabled class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition outline-none bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                <option value="">-- Pilih Semester Dahulu --</option>
                            </select>
                            <div id="mk-loading" class="hidden mt-2 text-xs text-blue-600 animate-pulse">
                                Memuat daftar mata kuliah berdasarkan semester...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Status -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-slate-800 font-bold border-b border-slate-100 pb-2">
                        <span class="bg-emerald-100 text-emerald-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">3</span>
                        <h3>Konfirmasi Pendaftaran</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Status Pendaftaran</label>
                            <div class="flex gap-4 py-3">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="status" value="Terdaftar" {{ (old('status', $dkbs->status)) == 'Terdaftar' ? 'checked' : '' }} class="w-4 h-4 text-blue-600">
                                    <span class="text-slate-700 group-hover:text-blue-600 transition text-sm">Terdaftar</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="status" value="Menunggu Antrean" {{ (old('status', $dkbs->status)) == 'Menunggu Antrean' ? 'checked' : '' }} class="w-4 h-4 text-blue-600">
                                    <span class="text-slate-700 group-hover:text-blue-600 transition text-sm">Antrean</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="status" value="Drop" {{ (old('status', $dkbs->status)) == 'Drop' ? 'checked' : '' }} class="w-4 h-4 text-red-600">
                                    <span class="text-slate-700 group-hover:text-red-600 transition text-sm">Drop</span>
                                </label>
                            </div>
                        </div>
                        <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-100">
                            <p class="text-[11px] text-amber-700 leading-relaxed font-medium">
                                <span class="font-bold">Info:</span> Ruangan dan kapasitas akan diperbarui otomatis jika Anda mengubah Mata Kuliah.
                            </p>
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

<!-- Scripts for Dynamic Behavior -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    // Initialize Tom Select for Mahasiswa
    new TomSelect("#select-mahasiswa", {
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        }
    });

    const semesterSelect = document.getElementById('select-semester');
    const mkSelect = document.getElementById('select-mk');
    const loadingEl = document.getElementById('mk-loading');
    const initialMk = "{{ old('kode_mk', $dkbs->kode_mk) }}";

    async function loadCourses(semester, selectedMk = null) {
        if (!semester) {
            mkSelect.innerHTML = '<option value="">-- Pilih Semester Dahulu --</option>';
            mkSelect.disabled = true;
            return;
        }

        // Reset and show loading
        mkSelect.innerHTML = '<option value="">-- Memuat Mata Kuliah... --</option>';
        mkSelect.disabled = true;
        loadingEl.classList.remove('hidden');

        try {
            const response = await fetch(`/admin/api/mata-kuliah/${semester}`);
            const courses = await response.json();
            
            mkSelect.innerHTML = '<option value="">-- Pilih Mata Kuliah --</option>';
            if (courses.length > 0) {
                courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.kode_mk;
                    option.textContent = `[${course.kode_mk}] ${course.nama_mk} (${course.sks} SKS)`;
                    if (selectedMk && course.kode_mk === selectedMk) {
                        option.selected = true;
                    }
                    mkSelect.appendChild(option);
                });
                mkSelect.disabled = false;
            } else {
                mkSelect.innerHTML = '<option value="">-- Tidak ada mata kuliah di semester ini --</option>';
            }
        } catch (error) {
            console.error('Error fetching courses:', error);
            mkSelect.innerHTML = '<option value="">-- Gagal memuat data --</option>';
        } finally {
            loadingEl.classList.add('hidden');
        }
    }

    semesterSelect.addEventListener('change', function() {
        loadCourses(this.value);
    });

    // Initial load for Edit
    window.addEventListener('DOMContentLoaded', () => {
        if (semesterSelect.value) {
            loadCourses(semesterSelect.value, initialMk);
        }
    });
</script>

<style>
    .ts-control {
        border-radius: 0.75rem !important;
        padding: 0.75rem 1rem !important;
        border-color: #e2e8f0 !important;
        background-color: #f8fafc !important;
    }
    .ts-wrapper.focus .ts-control {
        box-shadow: 0 0 0 2px #3b82f6 !important;
    }
</style>
@endsection