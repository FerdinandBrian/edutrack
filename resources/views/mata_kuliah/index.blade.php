@extends('layouts.mahasiswa')

@section('title','Mata Kuliah')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold">Mata Kuliah</h2>
    <p class="text-sm text-slate-500 mt-2">Daftar mata kuliah yang tersedia (diambil dari jadwal)</p>

    <div class="mt-4">
        @if($data->isEmpty())
            <div class="text-sm text-slate-500">Belum ada data mata kuliah.</div>
        @else
            <ul class="list-disc pl-6">
                @foreach($data as $mk)
                    <li class="py-1">{{ $mk }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
