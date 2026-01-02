@extends('layouts.dosen')

@section('title','Jadwal')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Jadwal</h2>
            <a href="/dosen/jadwal/create" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-emerald-100 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Jadwal
            </a>
    </div>

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
                        <a href="/dosen/jadwal/{{ $row->id }}" class="text-sm text-blue-600 mr-2">Lihat</a>
                        <a href="/dosen/jadwal/{{ $row->id }}/edit" class="text-sm text-yellow-600 mr-2">Edit</a>
                        <form action="/dosen/jadwal/{{ $row->id }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus jadwal?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-red-600">Hapus</button>
                        </form>
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
