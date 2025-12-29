@extends('layouts.dosen')

@section('title','Edit Jadwal')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Edit Jadwal</h2>

    @if($errors->any())
        <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="/jadwal/{{ $jadwal->id }}" method="POST" class="mt-4 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm text-slate-700 mb-1">Kode Mata Kuliah</label>
            <input name="kode_mk" value="{{ $jadwal->kode_mk }}" required class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Dosen (NIDN)</label>
            <select name="nidn" class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Dosen --</option>
                @foreach($dosens as $d)
                    <option value="{{ $d->nidn }}" {{ $d->nidn == $jadwal->nidn ? 'selected' : '' }}>{{ $d->nidn }} — {{ $d->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Hari</label>
            <input name="hari" value="{{ $jadwal->hari }}" required class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Jam</label>
            <input name="jam" value="{{ $jadwal->jam }}" required class="w-full border rounded px-3 py-2">
        </div>

        <div class="pt-4">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Perbarui</button>
            <a href="/jadwal" class="ml-3 text-sm text-slate-600">Batal</a>
        </div>
    </form>
</div>
@endsection