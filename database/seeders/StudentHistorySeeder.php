<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataKuliah;
use App\Models\Perkuliahan;
use App\Models\Dkbs;
use App\Models\Nilai;
use App\Models\Presensi;
use App\Models\Ruangan;
use App\Models\Dosen;

class StudentHistorySeeder extends Seeder
{
    public function run()
    {
        $nrp = '2472022'; // Bryan Christian
        $ruangan = Ruangan::first();
        $kode_ruangan = $ruangan ? $ruangan->kode_ruangan : 'H03A01';
        $dosen = Dosen::first();
        $nip = $dosen ? $dosen->nip : '12345';
        
        // --- Semester 1: 2024/2025 - Ganjil ---
        $sem1_ta = '2024/2025 - Ganjil';
        
        // Find suitable Sem 1 courses (MKU or others)
        // Try creating IF courses if they don't exist
        $mk1 = MataKuliah::firstOrCreate(
            ['kode_mk' => 'IF101'],
            ['nama_mk' => 'Pengantar Teknologi Informasi', 'sks' => 3, 'semester' => 1, 'jurusan' => 'Teknik Informatika', 'sifat' => 'Wajib']
        );
        $mk2 = MataKuliah::firstOrCreate(
            ['kode_mk' => 'IF102'],
            ['nama_mk' => 'Dasar Pemrograman', 'sks' => 4, 'semester' => 1, 'jurusan' => 'Teknik Informatika', 'sifat' => 'Wajib']
        );
        $mk_sem1 = collect([$mk1, $mk2]);

        foreach($mk_sem1 as $mk) {
            // Create Class (Perkuliahan) if not exists
            $kelas = Perkuliahan::firstOrCreate(
                ['kode_mk' => $mk->kode_mk, 'tahun_ajaran' => $sem1_ta],
                [
                    'hari' => 'Senin',
                    'jam_mulai' => '08:00',
                    'jam_berakhir' => '10:00',
                    'kelas' => 'A',
                    'kode_ruangan' => $kode_ruangan,
                    'nip_dosen' => $nip
                ]
            );

            // Enroll Student (DKBS)
            Dkbs::firstOrCreate(
                ['nrp' => $nrp, 'id_perkuliahan' => $kelas->id_perkuliahan],
                [
                    'kode_mk' => $mk->kode_mk,
                    'tahun_ajaran' => $sem1_ta,
                    'semester' => 1,
                    'status' => 'Terdaftar'
                ]
            );

            // Add Grade (Nilai)
            Nilai::updateOrCreate(
                ['nrp' => $nrp, 'kode_mk' => $mk->kode_mk],
                [
                    'p1'=>80, 'p2'=>85, 'uts'=>82, 'uas'=>85, 
                    'nilai_total'=>83, 'nilai_akhir'=>'A'
                ]
            );

            // Add Presensi (Attendance)
            Presensi::create([
                'nrp' => $nrp,
                'jadwal_id' => $kelas->id_perkuliahan,
                'tanggal' => '2024-09-01', // Dummy date
                'status' => 'Hadir'
            ]);
        }

        // --- Semester 2: 2024/2025 - Genap ---
        $sem2_ta = '2024/2025 - Genap';
        
        $mk3 = MataKuliah::firstOrCreate(
            ['kode_mk' => 'IF103'],
            ['nama_mk' => 'Algoritma Pemrograman', 'sks' => 4, 'semester' => 2, 'jurusan' => 'Teknik Informatika', 'sifat' => 'Wajib']
        );
        $mk_sem2 = collect([$mk3]);

        foreach($mk_sem2 as $mk) {
             // Create Class (Perkuliahan)
            $kelas = Perkuliahan::firstOrCreate(
                ['kode_mk' => $mk->kode_mk, 'tahun_ajaran' => $sem2_ta],
                [
                    'hari' => 'Selasa',
                    'jam_mulai' => '10:00',
                    'jam_berakhir' => '12:00',
                    'kelas' => 'A',
                    'kode_ruangan' => $kode_ruangan,
                    'nip_dosen' => $nip
                ]
            );

            // Enroll Student (DKBS)
            Dkbs::firstOrCreate(
                ['nrp' => $nrp, 'id_perkuliahan' => $kelas->id_perkuliahan],
                [
                    'kode_mk' => $mk->kode_mk,
                    'tahun_ajaran' => $sem2_ta,
                    'semester' => 2,
                    'status' => 'Terdaftar'
                ]
            );

            // Add Grade (Nilai)
            Nilai::updateOrCreate(
                ['nrp' => $nrp, 'kode_mk' => $mk->kode_mk],
                [
                    'p1'=>75, 'p2'=>78, 'uts'=>80, 'uas'=>82, 
                    'nilai_total'=>80, 'nilai_akhir'=>'A-'
                ]
            );
             // Add Presensi (Attendance)
            Presensi::create([
                'nrp' => $nrp,
                'jadwal_id' => $kelas->id_perkuliahan,
                'tanggal' => '2025-02-01', // Dummy date
                'status' => 'Hadir'
            ]);
        }
    }
}
