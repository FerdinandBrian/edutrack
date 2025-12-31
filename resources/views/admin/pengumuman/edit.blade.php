@extends('layouts.admin')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="/admin/pengumuman" class="text-sm text-slate-500 hover:text-blue-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-xl shadow p-8">
        <h2 class="text-xl font-bold text-slate-800 mb-6">Edit Pengumuman</h2>

        <form action="/admin/pengumuman/{{ $pengumuman->id }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Judul Pengumuman</label>
                <input type="text" name="judul" value="{{ $pengumuman->judul }}" required class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Isi Pengumuman</label>
                <textarea name="isi" rows="4" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ $pengumuman->isi }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Kategori</label>
                    <select name="kategori" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="umum" {{ $pengumuman->kategori == 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="akademik" {{ $pengumuman->kategori == 'akademik' ? 'selected' : '' }}>Akademik</option>
                        <option value="libur" {{ $pengumuman->kategori == 'libur' ? 'selected' : '' }}>Libur</option>
                        <option value="kegiatan" {{ $pengumuman->kategori == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Waktu Mulai</label>
                    <input type="date" name="waktu_mulai" value="{{ $pengumuman->waktu_mulai->format('Y-m-d') }}" required class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Waktu Selesai</label>
                    <input type="date" name="waktu_selesai" value="{{ $pengumuman->waktu_selesai ? $pengumuman->waktu_selesai->format('Y-m-d') : '' }}" required class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
