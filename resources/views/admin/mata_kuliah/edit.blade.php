@extends('layouts.admin')

@section('title', 'Edit Mata Kuliah')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="bg-amber-600 px-8 py-6">
            <div class="flex items-center gap-4">
                <a href="/admin/mata-kuliah" class="text-white/60 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-xl font-bold text-white">Edit Mata Kuliah</h2>
            </div>
        </div>

        <div class="p-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="/admin/mata-kuliah/{{ $mk->kode_mk }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-2">Kode Mata Kuliah</label>
                    <input type="text" value="{{ $mk->kode_mk }}" disabled class="w-full bg-slate-100 text-slate-500 border border-slate-200 rounded-xl px-4 py-3 cursor-not-allowed">
                    <p class="text-[10px] text-slate-400 mt-1 italic">* Kode MK tidak dapat diubah.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-2">Nama Mata Kuliah</label>
                    <input type="text" name="nama_mk" required placeholder="Contoh: Pemrograman Web" value="{{ old('nama_mk', $mk->nama_mk) }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition outline-none bg-slate-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-2">Program Studi (Jurusan)</label>
                    <select name="jurusan" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition outline-none bg-slate-50">
                        <option value="">-- Pilih Program Studi --</option>
                        @php $current = old('jurusan', $mk->jurusan); @endphp
                        <option value="Teknik Informatika" {{ $current == 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                        <option value="Sistem Komputer" {{ $current == 'Sistem Komputer' ? 'selected' : '' }}>Sistem Komputer</option>
                        <option value="Sistem Informasi" {{ $current == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                        <option value="Teknologi Informasi" {{ $current == 'Teknologi Informasi' ? 'selected' : '' }}>Teknologi Informasi</option>
                        <option value="Bisnis Digital" {{ $current == 'Bisnis Digital' ? 'selected' : '' }}>Bisnis Digital</option>
                        <option value="Psikologi" {{ $current == 'Psikologi' ? 'selected' : '' }}>Psikologi</option>
                        <option value="Desain Komunikasi Visual" {{ $current == 'Desain Komunikasi Visual' ? 'selected' : '' }}>Desain Komunikasi Visual</option>
                        <option value="Seni Rupa Murni" {{ $current == 'Seni Rupa Murni' ? 'selected' : '' }}>Seni Rupa Murni</option>
                        <option value="Arsitektur" {{ $current == 'Arsitektur' ? 'selected' : '' }}>Arsitektur</option>
                        <option value="Fashion Design" {{ $current == 'Fashion Design' ? 'selected' : '' }}>Fashion Design</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-2">Sifat Mata Kuliah</label>
                    <div class="flex items-center gap-6 mt-2">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="radio" name="sifat" value="Wajib" class="peer sr-only" {{ old('sifat', $mk->sifat) == 'Wajib' ? 'checked' : '' }}>
                                <div class="w-5 h-5 border-2 border-slate-300 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 transition"></div>
                            </div>
                            <span class="text-slate-600 font-medium group-hover:text-blue-600 transition">Wajib</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="radio" name="sifat" value="Pilihan" class="peer sr-only" {{ old('sifat', $mk->sifat) == 'Pilihan' ? 'checked' : '' }}>
                                <div class="w-5 h-5 border-2 border-slate-300 rounded-full peer-checked:border-amber-500 peer-checked:bg-amber-500 transition"></div>
                            </div>
                            <span class="text-slate-600 font-medium group-hover:text-amber-600 transition">Pilihan</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">SKS</label>
                        <input type="number" name="sks" required min="1" max="8" value="{{ old('sks', $mk->sks) }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition outline-none bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Semester</label>
                        <input type="number" name="semester" required min="1" max="8" value="{{ old('semester', $mk->semester) }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition outline-none bg-slate-50">
                    </div>
                </div>

                <div class="pt-8 flex items-center justify-end gap-4 border-t border-slate-50">
                    <a href="/admin/mata-kuliah" class="px-6 py-3 text-slate-500 hover:text-slate-700 font-medium transition">Batal</a>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-10 py-3 rounded-xl shadow-lg shadow-amber-200 transition transform hover:-translate-y-0.5 active:translate-y-0">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
