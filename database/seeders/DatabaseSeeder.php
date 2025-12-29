<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin
        $userAdmin = User::create([
            'nama' => 'Super Admin',
            'email' => 'admin@edutrack.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        Admin::create([
            'kode_admin' => 'ADM001',
            'user_id' => $userAdmin->id,
            'nama' => 'Super Admin',
            'email' => 'admin@edutrack.com',
            'password' => Hash::make('admin123'),
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '1990-01-01',
            'no_telepon' => '08123456789',
        ]);

        // 2. Dosen
        $userDosen = User::create([
            'nama' => 'Dr. Budi Santoso',
            'email' => 'budi@edutrack.com',
            'password' => Hash::make('dosen123'),
            'role' => 'dosen',
        ]);

        Dosen::create([
            'nip' => 'DSN123',
            'user_id' => $userDosen->id,
            'nama' => 'Dr. Budi Santoso',
            'email' => 'budi@edutrack.com',
            'password' => Hash::make('dosen123'),
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '1985-05-20',
            'no_telepon' => '08987654321',
            'fakultas' => 'Informatika',
        ]);

        // 3. Mahasiswa
        $userMhs = User::create([
            'nama' => 'Ferdinand Brian',
            'email' => 'ferdinand@edutrack.com',
            'password' => Hash::make('mhs123'),
            'role' => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'nrp' => 'MHS001',
            'user_id' => $userMhs->id,
            'nama' => 'Ferdinand Brian',
            'email' => 'ferdinand@edutrack.com',
            'password' => Hash::make('mhs123'),
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2004-10-15',
            'alamat' => 'Jl. Kebon Jeruk No. 12',
            'no_telepon' => '087712345678',
            'jurusan' => 'Teknik Informatika',
        ]);
    }
}
