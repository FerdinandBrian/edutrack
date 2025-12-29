@extends('layouts.admin')

@section('title','Edit DKBS')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Edit DKBS</h2>

    @if($errors->any())
        <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="/dkbs/{{ $dkbs->id }}" method="POST" class="mt-4 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm text-slate-700 mb-1">NRP</label>
            <select name="nrp" required class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Mahasiswa --</option>
                @foreach($mahasiswas as $m)
                    <option value="{{ $m->nrp }}" {{ $m->nrp == $dkbs->nrp ? 'selected' : '' }}>{{ $m->nrp }} — {{ $m->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Kode Mata Kuliah</label>
            <input name="kode_mk" value="{{ $dkbs->kode_mk }}" required class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Semester</label>
            <input name="semester" value="{{ $dkbs->semester }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Status</label>
            <input name="status" value="{{ $dkbs->status }}" class="w-full border rounded px-3 py-2" placeholder="Terdaftar / Drop">
        </div>

        <div class="pt-4">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Perbarui</button>
            <a href="/dkbs" class="ml-3 text-sm text-slate-600">Batal</a>
        </div>
    </form>
</div>
@endsection