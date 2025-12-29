@extends('layouts.dosen')

@section('title','Daftar Nilai')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Daftar Nilai</h2>
            <a href="/dosen/nilai/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Tambah Nilai</a>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-sm text-slate-600 border-b">
                    <th class="py-3">#</th>
                    <th>NRP</th>
                    <th>Nama</th>
                    <th>Mata Kuliah</th>
                    <th>Nilai</th>
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
                    <td>{{ $row->nilai }}</td>
                    <td class="text-right">
                        <a href="/dosen/nilai/{{ $row->id }}" class="text-sm text-blue-600 mr-2">Lihat</a>
                        <a href="/dosen/nilai/{{ $row->id }}/edit" class="text-sm text-yellow-600 mr-2">Edit</a>
                        <form action="/dosen/nilai/{{ $row->id }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus nilai ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-500">Belum ada data nilai.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
