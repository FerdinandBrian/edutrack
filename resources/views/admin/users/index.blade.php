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
                @php $lastRole = null; @endphp
                @foreach($users as $user)
                    @if($lastRole !== $user->role)
                        <tr class="bg-slate-50/80">
                            <td colspan="5" class="px-6 py-2">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    {{ $user->role === 'admin' ? 'Administrator' : ($user->role === 'dosen' ? 'Dosen / Tenaga Pendidik' : 'Mahasiswa') }}
                                </span>
                            </td>
                        </tr>
                        @php $lastRole = $user->role; @endphp
                    @endif
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 font-medium text-slate-900 font-mono text-xs">{{ $user->identifier }}</td>
                    <td class="px-6 py-4 text-slate-600 font-semibold">{{ $user->nama }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @php
                            $roleName = ucfirst($user->role);
                            $color = match($user->role) {
                                'admin' => 'bg-rose-50 text-rose-600 border-rose-100',
                                'dosen' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'mahasiswa' => 'bg-blue-50 text-blue-600 border-blue-100',
                                default => 'bg-slate-50 text-slate-700 border-slate-200'
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $color }}">
                            {{ strtoupper($roleName) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @php
                            $currentAdmin = \App\Models\Admin::where('user_id', auth()->id())->first();
                            $isSecondAdmin = $currentAdmin && $currentAdmin->admin_level === 'second';
                            
                            $targetAdmin = null;
                            if($user->role === 'admin') {
                                $targetAdmin = \App\Models\Admin::where('kode_admin', $user->identifier)->first();
                            }
                            $isSuperAdmin = $targetAdmin && $targetAdmin->admin_level === 'super';
                            
                            // Second admin cannot edit/delete super admin
                            $canModify = !($isSecondAdmin && $isSuperAdmin);
                        @endphp
                        
                        <div class="flex items-center justify-end gap-3">
                            @if($canModify)
                                <a href="/admin/users/{{ $user->id }}/edit" class="text-amber-600 hover:text-amber-700 font-bold text-xs uppercase tracking-wider bg-amber-50 px-3 py-1 rounded-lg border border-amber-100 transition">Edit</a>
                                @if($user->id != auth()->id())
                                <form action="/admin/users/{{ $user->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-rose-600 hover:text-rose-700 font-bold text-xs uppercase tracking-wider bg-rose-50 px-3 py-1 rounded-lg border border-rose-100 transition">Hapus</button>
                                </form>
                                @endif
                            @else
                                <span class="text-slate-400 text-xs italic">Anda Tidak Memiliki Akses</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
