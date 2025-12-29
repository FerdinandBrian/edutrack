@extends('layouts.dosen')

@section('title','Daftar Presensi')

@section('content')

<div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Daftar Presensi</h2>
        <a href="/dosen/presensi/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Tambah Presensi</a>
    </div>

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
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $row)
                <tr class="border-b">
                    <td class="py-3">{{ $i + 1 }}</td>
                    <td>{{ $row->nrp }}</td>
                    <td>{{ optional($row->mahasiswa)->nama }}</td>
                    <td>{{ $row->tanggal }}</td>
                    <td>{{ $row->status }}</td>
                    <td>{{ $row->keterangan }}</td>
                    <td class="text-right">
                        <a href="/dosen/presensi/{{ $row->id }}" class="text-sm text-blue-600 mr-2">Lihat</a>
                        <a href="/dosen/presensi/{{ $row->id }}/edit" class="text-sm text-yellow-600 mr-2">Edit</a>
                        <form action="/dosen/presensi/{{ $row->id }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus presensi ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-slate-500">Belum ada data presensi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
