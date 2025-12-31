@extends('layouts.dosen')

@section('title', 'Edit Profil')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">Edit Profil Dosen</h2>
        </div>

        <form action="/dosen/profile" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $user->nama) }}" 
                       class="w-full rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500">
                @error('nama')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                       class="w-full rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- No Telepon -->
                <div>
                    <label for="no_telepon" class="block text-sm font-semibold text-slate-700 mb-1">No Telepon</label>
                    <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon', $user->dosen->no_telepon ?? '') }}" 
                           class="w-full rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500">
                    @error('no_telepon')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-sm font-semibold text-slate-700 mb-1">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" 
                          class="w-full rounded-lg border-slate-300 focus:border-green-500 focus:ring-green-500">{{ old('alamat', $user->dosen->alamat ?? '') }}</textarea>
                @error('alamat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="/dosen/profile" class="px-4 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg font-medium transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition-colors shadow-lg shadow-green-200">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
