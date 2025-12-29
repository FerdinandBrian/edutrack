@extends(auth()->user()->id_role == 2 ? 'layouts.dosen' : (auth()->user()->id_role == 3 ? 'layouts.mahasiswa' : 'layouts.admin'))

@section('title','Jadwal')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Jadwal</h2>
        @if(auth()->user()->id_role == 2)
            <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Tambah Jadwal</a>
        @endif
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
                        <a href="/jadwal/{{ $row->id }}" class="text-sm text-blue-600 mr-2">Lihat</a>
                        @if(auth()->user()->id_role == 2)
                            <a href="#" class="text-sm text-yellow-600 mr-2">Edit</a>
                            <form action="#" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm text-red-600">Hapus</button>
                            </form>
                        @endif
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
