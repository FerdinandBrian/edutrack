@extends('layouts.admin')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Perbarui Pengumuman</h2>
            <p class="text-slate-500 mt-1 text-sm">Sesuaikan detail agenda atau pengumuman kampus.</p>
        </div>
        <a href="/admin/pengumuman" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 hover:text-blue-600 hover:bg-white rounded-xl transition-all border border-slate-200 hover:border-blue-100 bg-slate-50 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-8 lg:p-10">
            <form action="/admin/pengumuman/{{ $pengumuman->id }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Informasi Dasar</label>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Judul Pengumuman</label>
                                <input type="text" name="judul" value="{{ $pengumuman->judul }}" required 
                                    class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all py-3 px-4 text-slate-700 placeholder:text-slate-400"
                                    placeholder="Contoh: Libur Hari Raya Natal">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Isi Pengumuman</label>
                                <textarea name="isi" rows="6" 
                                    class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all py-3 px-4 text-slate-700 placeholder:text-slate-400"
                                    placeholder="Tuliskan detail pengumuman di sini...">{{ $pengumuman->isi }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Klasifikasi & Waktu</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Kategori</label>
                                <select name="kategori" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all py-3 px-4 text-slate-700">
                                    <option value="umum" {{ $pengumuman->kategori == 'umum' ? 'selected' : '' }}>Umum</option>
                                    <option value="akademik" {{ $pengumuman->kategori == 'akademik' ? 'selected' : '' }}>Akademik</option>
                                    <option value="libur" {{ $pengumuman->kategori == 'libur' ? 'selected' : '' }}>Libur</option>
                                    <option value="kegiatan" {{ $pengumuman->kategori == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                                </select>
                            </div>
                            <div class="hidden md:block"></div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Waktu Mulai</label>
                                <input type="date" name="waktu_mulai" value="{{ $pengumuman->waktu_mulai->format('Y-m-d') }}" required 
                                    class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all py-3 px-4 text-slate-700">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Waktu Selesai</label>
                                <input type="date" name="waktu_selesai" value="{{ $pengumuman->waktu_selesai ? $pengumuman->waktu_selesai->format('Y-m-d') : '' }}" required 
                                    class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all py-3 px-4 text-slate-700">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-xs text-slate-400 italic">* Perubahan akan langsung terlihat oleh semua Mahasiswa dan Dosen.</p>
                    <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 text-white px-8 py-3.5 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
