@extends('layouts.mahasiswa')

@section('title','Pembayaran')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Pembayaran</h2>
        @if(auth()->user()->id_role == 1)
            <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Buat Tagihan</a>
        @endif
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left text-sm text-slate-600 border-b">
                    <th class="py-3">#</th>
                    <th>NRP</th>
                    <th>Nama</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $row)
                <tr class="border-b">
                    <td class="py-3">{{ $i + 1 }}</td>
                    <td>{{ $row->nrp }}</td>
                    <td>{{ optional($row->mahasiswa)->nama }}</td>
                    <td>{{ $row->jumlah ?? '-' }}</td>
                    <td>{{ $row->status ?? 'Belum Lunas' }}</td>
                    <td class="text-right">
                        <a href="/pembayaran/{{ $row->id }}" class="text-sm text-blue-600 mr-2">Lihat</a>
                        @if(auth()->user()->id_role == 1)
                            <a href="#" class="text-sm text-yellow-600 mr-2">Edit</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-500">Belum ada data pembayaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
