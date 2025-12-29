@extends('layouts.mahasiswa')

@section('title','Jadwal')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold mb-4">Jadwal Kuliah</h2>

    <div class="mt-4 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-sm text-slate-600 border-b">
                    <th class="py-3">#</th>
                    <th>Kode MK</th>
                    <th>Dosen</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $row)
                <tr class="border-b">
                    <td class="py-3">{{ $i + 1 }}</td>
                    <td>{{ $row->kode_mk }}</td>
                    <td>{{ optional($row->dosen)->nama ?? $row->nidn }}</td>
                    <td>{{ $row->hari }}</td>
                    <td>{{ $row->jam }}</td>
                    <td class="text-right">
                        <a href="/mahasiswa/jadwal/{{ $row->id }}" class="text-sm text-blue-600">Lihat Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-500">Belum ada data jadwal.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
