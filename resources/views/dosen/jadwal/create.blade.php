@extends('layouts.dosen')

@section('title','Tambah Jadwal')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Tambah Jadwal</h2>

    @if($errors->any())
        <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
            {{ $errors->first() }}
        </div>
    @endif

@if(isset($mataKuliahs))
    <!-- Dosen View -->
    <form action="/dosen/jadwal" method="POST" class="mt-4 space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-slate-700 mb-1 font-bold">Mata Kuliah</label>
            <select name="kode_mk" required class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                @foreach($mataKuliahs as $mk)
                    <option value="{{ $mk->kode_mk }}">{{ $mk->nama_mk }} ({{ $mk->kode_mk }})</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 mt-1">*Hanya menampilkan mata kuliah yang Anda ampu.</p>
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1 font-bold">Hari</label>
            <select name="hari" required class="w-full border border-slate-300 rounded-lg px-3 py-2">
                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-700 mb-1 font-bold">Jam Mulai</label>
                <input type="time" name="jam_mulai" required class="w-full border border-slate-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-slate-700 mb-1 font-bold">Jam Berakhir</label>
                <input type="time" name="jam_berakhir" required class="w-full border border-slate-300 rounded-lg px-3 py-2">
            </div>
        </div>

        <div class="pt-4 flex items-center gap-3">
            <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-semibold transition">Simpan Jadwal</button>
            <a href="/dosen/jadwal" class="text-sm text-slate-600 hover:text-slate-800">Batal</a>
        </div>
    </form>
    @else
    <!-- Fallback/Admin View (Original) -->
    <form action="/dosen/jadwal" method="POST" class="mt-4 space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-slate-700 mb-1">Kode Mata Kuliah</label>
            <input name="kode_mk" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-slate-700 mb-1">Dosen (NIDN)</label>
            <input name="nidn" class="w-full border rounded px-3 py-2">
        </div>
        <div>
             <label class="block text-sm text-slate-700 mb-1">Hari & Jam</label>
             <input name="hari" placeholder="Senin" class="border rounded px-3 py-2 w-1/3">
             <input name="jam" placeholder="07:00" class="border rounded px-3 py-2 w-1/3">
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
    </form>
    @endif
</div>
@endsection
