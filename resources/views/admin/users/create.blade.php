@extends('layouts.admin')

@section('title', 'Tambah User Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">
        <div class="flex items-center gap-4 mb-8">
            @php
                $backLink = match(request('role')) {
                    'admin' => '/admin/admin-data',
                    'dosen' => '/admin/dosen',
                    'mahasiswa' => '/admin/mahasiswa',
                    default => '/admin/dashboard',
                };
            @endphp
            <a href="{{ $backLink }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                ←
            </a>
            <h2 class="text-xl font-bold text-slate-800">Tambah User Baru</h2>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/admin/users" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Role</label>
                    <select name="role" id="roleSelect" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                        <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div>
                    <label id="identifierLabel" class="block text-sm font-medium text-slate-700 mb-2">NRP</label>
                    <input type="text" name="identifier" required placeholder="ID" value="{{ old('identifier') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <!-- Admin Level Selection (Only for Super Admin creating new Admin) -->
            <div id="adminLevelField" style="display: none;">
                <label class="block text-sm font-medium text-slate-700 mb-2">Level Admin</label>
                <select name="admin_level" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                    <option value="second">Second Admin (Akses Terbatas)</option>
                    <option value="super">Super Admin (Akses Penuh)</option>
                </select>
                <p class="text-xs text-slate-500 mt-1">Super Admin dapat mengelola semua data termasuk admin lain. Second Admin hanya dapat melihat data.</p>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" required placeholder="Nama Lengkap" value="{{ old('nama') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                    <input type="email" name="email" required placeholder="email@example.com" value="{{ old('email') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jenis Kelamin</label>
                    <select name="jenis_kelamin" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" required value="{{ old('tanggal_lahir') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">No Telepon</label>
                    <input type="text" name="no_telepon" placeholder="08..." value="{{ old('no_telepon') }}" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div id="fakultasField" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fakultas</label>
                    <input type="text" name="fakultas" placeholder="Fakultas Teknologi Informasi" value="{{ old('fakultas') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div id="jurusanField">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jurusan</label>
                    <input type="text" name="jurusan" placeholder="Teknik Informatika" value="{{ old('jurusan') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Alamat</label>
                <textarea name="alamat" rows="2" placeholder="Alamat lengkap" class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">{{ old('alamat') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-6 border-t pt-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <input type="password" name="password" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 transition">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-700 hover:-translate-y-1 transition active:translate-y-0 text-lg">
                    Simpan User Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const roleSelect = document.getElementById('roleSelect');
    const identifierLabel = document.getElementById('identifierLabel');
    const jurusanField = document.getElementById('jurusanField');
    const fakultasField = document.getElementById('fakultasField');
    const adminLevelField = document.getElementById('adminLevelField');

    roleSelect.addEventListener('change', function() {
        const val = this.value;
        
        // Label Identifier and Fields Visibility
        if(val === 'mahasiswa') {
            identifierLabel.innerText = 'NRP';
            jurusanField.classList.remove('hidden');
            fakultasField.classList.add('hidden');
            adminLevelField.style.display = 'none';
            // Change label for Mahasiswa if needed or keep generic "Jurusan"
            jurusanField.querySelector('label').innerText = 'Jurusan';
        } else if(val === 'dosen') {
            identifierLabel.innerText = 'NIP';
            jurusanField.classList.remove('hidden'); // Enable Jurusan for Dosen
            fakultasField.classList.remove('hidden');
            adminLevelField.style.display = 'none';
            // Change label for Dosen
             jurusanField.querySelector('label').innerText = 'Program Studi (Jurusan)';
        } else {
            identifierLabel.innerText = 'Kode Admin';
            jurusanField.classList.add('hidden');
            fakultasField.classList.add('hidden');
            adminLevelField.style.display = 'block'; // Show admin level field
        }
    });

    // Run once on load
    roleSelect.dispatchEvent(new Event('change'));
</script>
@endsection
