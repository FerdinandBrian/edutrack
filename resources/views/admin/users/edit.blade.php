@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="bg-amber-600 px-8 py-8 text-white">
            <div class="flex items-center gap-4">
                @php
                    $backLink = match($user->role) {
                        'admin' => '/admin/admin-data',
                        'dosen' => '/admin/dosen',
                        'mahasiswa' => '/admin/mahasiswa',
                        default => '/admin/dashboard',
                    };
                @endphp
                <a href="{{ $backLink }}" class="hover:bg-white/20 p-2 rounded-full transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold">Edit Profil Pengguna</h2>
                    <p class="text-amber-100 text-sm mt-1">Memperbarui data akun untuk <span class="font-bold underline">{{ $user->nama }}</span> ({{ strtoupper($user->role) }})</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-medium">{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="/admin/users/{{ $user->id }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Section: Akun Login -->
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-8 h-px bg-slate-200"></span>
                        Akun & Role
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Role</label>
                            <input type="text" value="{{ strtoupper($user->role) }}" disabled class="w-full bg-slate-200 border border-slate-300 text-slate-500 rounded-xl px-4 py-3 cursor-not-allowed font-bold">
                            <p class="text-[10px] text-slate-400 mt-1 italic">* Role tidak dapat diubah setelah pembuatan.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">NRP / NIP / Kode Admin</label>
                            <input type="text" value="{{ $user->identifier }}" disabled class="w-full bg-slate-200 border border-slate-300 text-slate-500 rounded-xl px-4 py-3 cursor-not-allowed font-bold">
                        </div>
                    </div>
                </div>

                <!-- Section: Detail Profil -->
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-8 h-px bg-slate-200"></span>
                        Informasi Pribadi
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Jenis Kelamin</label>
                            <select name="jenis_kelamin" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-white">
                                <option value="Laki-laki" {{ old('jenis_kelamin', $detail->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $detail->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $detail->tanggal_lahir ?? '') }}" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">No Telepon</label>
                            <input type="text" name="no_telepon" value="{{ old('no_telepon', $detail->no_telepon ?? '') }}" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none">
                        </div>

                        <!-- Role Specific Fields -->
                        @if($user->role === 'mahasiswa')
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Jurusan</label>
                            <input type="text" name="jurusan" value="{{ old('jurusan', $detail->jurusan ?? '') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none">
                        </div>
                        @elseif($user->role === 'dosen')
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Fakultas</label>
                            <input type="text" name="fakultas" value="{{ old('fakultas', $detail->fakultas ?? '') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none">
                        </div>
                        @endif

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Alamat Lengkap</label>
                            <textarea name="alamat" rows="3" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none">{{ old('alamat', $detail->alamat ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section: Keamanan -->
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="w-8 h-px bg-slate-200"></span>
                        Keamanan (Ganti Password)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-amber-50 p-6 rounded-2xl border border-amber-100">
                        <div class="md:col-span-2">
                            <p class="text-xs text-amber-700 mb-4 bg-white p-3 rounded-lg border border-amber-200">
                                💡 Kosongkan password jika Anda tidak berniat menggantinya.
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Password Baru</label>
                            <input type="password" name="password" placeholder="••••••••" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 transition outline-none bg-white">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-8 flex items-center justify-end gap-4 border-t border-slate-50">
                    <a href="{{ $backLink }}" class="px-6 py-3 text-slate-500 hover:text-slate-700 font-medium transition">Batal</a>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-10 py-3 rounded-xl shadow-lg shadow-amber-200 transition transform hover:-translate-y-0.5 active:translate-y-0">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
