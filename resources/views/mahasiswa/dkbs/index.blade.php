@extends('layouts.mahasiswa')

@section('title','DKBS')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold mb-4">DKBS - Daftar Kelas & Beban Studi</h2>

    @if(session('success'))
        <div class="mt-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-4 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-sm text-slate-600 border-b">
                    <th class="py-3">#</th>
                    <th>NRP</th>
                    <th>Nama</th>
                    <th>Kode MK</th>
                    <th>Semester</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $row)
                <tr class="border-b">
                    <td class="py-3">{{ $i + 1 }}</td>
                    <td>{{ $row->nrp }}</td>
                    <td>{{ optional($row->mahasiswa)->nama }}</td>
                    <td>{{ $row->kode_mk }}</td>
                    <td>{{ $row->semester }}</td>
                    <td class="text-right">
                        <span class="text-xs text-slate-400 italic">View Only</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-500">Belum ada data DKBS.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection