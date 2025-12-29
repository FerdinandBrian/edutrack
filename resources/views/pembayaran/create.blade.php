@extends('layouts.admin')

@section('title','Buat Tagihan')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Buat Tagihan</h2>

    @if($errors->any())
        <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="/pembayaran" method="POST" class="mt-4 space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-slate-700 mb-1">NRP</label>
            <select name="nrp" required class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Mahasiswa --</option>
                @foreach($mahasiswas as $m)
                    <option value="{{ $m->nrp }}">{{ $m->nrp }} — {{ $m->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Jenis</label>
            <input name="jenis" required class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Jumlah</label>
            <input name="jumlah" required type="number" step="0.01" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Status</label>
            <input name="status" class="w-full border rounded px-3 py-2" placeholder="Belum Lunas / Lunas">
        </div>

        <div class="pt-4">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="/pembayaran" class="ml-3 text-sm text-slate-600">Batal</a>
        </div>
    </form>
</div>
@endsection
