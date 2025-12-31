@extends('layouts.admin')

@section('title', 'Buat Pengumuman')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="/admin/pengumuman" class="text-sm text-slate-500 hover:text-blue-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-xl shadow p-8">
        <h2 class="text-xl font-bold text-slate-800 mb-6">Buat Pengumuman Baru</h2>

        <form action="/admin/pengumuman" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Judul Pengumuman</label>
                <input type="text" name="judul" required class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Jadwal Libur Semester Ganjil">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Isi Pengumuman</label>
                <textarea name="isi" rows="4" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Detail pengumuman..."></textarea>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Kategori</label>
                    <select name="kategori" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="umum">Umum</option>
                        <option value="akademik">Akademik</option>
                        <option value="libur">Libur</option>
                        <option value="kegiatan">Kegiatan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Waktu Mulai</label>
                    <input type="date" name="waktu_mulai" required class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Waktu Selesai</label>
                    <input type="date" name="waktu_selesai" required class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                    Simpan Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
