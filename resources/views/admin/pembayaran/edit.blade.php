@extends('layouts.admin')

@section('title','Edit Tagihan')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Edit Tagihan</h2>

    @if($errors->any())
        <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="/admin/pembayaran/{{ $tagihan->id }}" method="POST" class="mt-4 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm text-slate-700 mb-1">NRP</label>
            <select name="nrp" required class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Mahasiswa --</option>
                @foreach($mahasiswas as $m)
                    <option value="{{ $m->nrp }}" {{ $m->nrp == $tagihan->nrp ? 'selected' : '' }}>{{ $m->nrp }} — {{ $m->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Jenis</label>
            <input name="jenis" value="{{ $tagihan->jenis }}" required class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Jumlah</label>
            <input name="jumlah" value="{{ $tagihan->jumlah }}" required type="number" step="0.01" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Batas Pembayaran</label>
            <input name="batas_pembayaran" value="{{ $tagihan->batas_pembayaran }}" type="date" required class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="Belum Lunas" {{ $tagihan->status == 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="Lunas" {{ $tagihan->status == 'Lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>

        <div class="pt-4">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Perbarui</button>
            <a href="/admin/pembayaran" class="ml-3 text-sm text-slate-600">Batal</a>
        </div>
    </form>
</div>
@endsection