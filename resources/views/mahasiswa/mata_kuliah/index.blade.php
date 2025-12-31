@extends('layouts.mahasiswa')

@section('title','Mata Kuliah')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold">Mata Kuliah</h2>
    <p class="text-sm text-slate-500 mt-2">Daftar mata kuliah yang tersedia (diambil dari jadwal)</p>

    <div class="mt-4">
        @if($data->isEmpty())
            <div class="text-sm text-slate-500">Belum ada mata kuliah yang diambil.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b text-slate-500">
                            <th class="py-2">Kode MK</th>
                            <th class="py-2">Mata Kuliah</th>
                            <th class="py-2 text-center">SKS</th>
                            <th class="py-2 text-center">Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                            <tr class="border-b last:border-0 hover:bg-slate-50">
                                <td class="py-3 font-mono text-xs">{{ $row->kode_mk }}</td>
                                <td class="py-3 font-medium">{{ optional($row->mataKuliah)->nama_mk ?? '-' }}</td>
                                <td class="py-3 text-center">{{ optional($row->mataKuliah)->sks ?? '-' }}</td>
                                <td class="py-3 text-center">{{ optional($row->mataKuliah)->semester ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
