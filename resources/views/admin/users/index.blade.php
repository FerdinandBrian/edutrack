@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold">Daftar User</h2>
        <a href="/admin/users/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
            + Tambah User (Admin/Dosen/Mahasiswa)
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b">
                <tr>
                    <th class="px-6 py-4">NRP/NIP/Kode</th>
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($users as $user)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 font-medium text-slate-900">{{ $user->identifier }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $user->nama }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @php
                            $roleName = ucfirst($user->role);
                            $color = match($user->role) {
                                'admin' => 'bg-red-100 text-red-700',
                                'dosen' => 'bg-green-100 text-green-700',
                                'mahasiswa' => 'bg-blue-100 text-blue-700',
                                default => 'bg-slate-100 text-slate-700'
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $color }}">
                            {{ $roleName }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                        <a href="/admin/users/{{ $user->id }}/edit" class="text-amber-600 hover:text-amber-900 font-medium">Edit</a>
                        @if($user->id != auth()->id())
                        <form action="/admin/users/{{ $user->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
