<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perkuliahan;
use Illuminate\Support\Facades\DB;

class Semester3ScheduleSeeder extends Seeder
{
    public function run()
    {
        // 1. Define Room Map
        $roomMap = [
            'Lab MMD' => 'L8005',
            'Lab ADV 1' => 'L8001',
            'Lab ADV 2' => 'L8002',
            'Lab ADV 3' => 'L8003',
            'Lab ADV 4' => 'L8004',
            'Lab DB' => 'L8006', // Assuming Lab Inter
        ];

        // 2. Define Dosen List (Round Robin Assignment)
        $dosens = ['0072201', '0072202', '0072203', '0072204', '0072205', '0072206', '0072207'];
        $dosenIndex = 0;

        // 3. Define Schedules based on Images
        $schedules = [
            // IN230 Rekayasa Perangkat Lunak (MK003)
            ['kode_mk' => 'MK003', 'kelas' => 'A', 'hari' => 'Rabu', 'jam_mulai' => '07:00', 'jam_berakhir' => '09:30', 'ruang' => 'Lab MMD'],
            ['kode_mk' => 'MK003', 'kelas' => 'B', 'hari' => 'Selasa', 'jam_mulai' => '15:00', 'jam_berakhir' => '17:30', 'ruang' => 'Lab MMD'],

            // IN231 Teknologi Multimedia (MK004)
            ['kode_mk' => 'MK004', 'kelas' => 'A', 'hari' => 'Senin', 'jam_mulai' => '07:00', 'jam_berakhir' => '08:40', 'ruang' => 'Lab ADV 2'],
            ['kode_mk' => 'MK004', 'kelas' => 'B', 'hari' => 'Senin', 'jam_mulai' => '09:30', 'jam_berakhir' => '11:40', 'ruang' => 'Lab ADV 2'],
            ['kode_mk' => 'MK004', 'kelas' => 'C', 'hari' => 'Senin', 'jam_mulai' => '12:30', 'jam_berakhir' => '14:10', 'ruang' => 'Lab ADV 2'],

            // IN232 Matematika Diskrit (MK005)
            ['kode_mk' => 'MK005', 'kelas' => 'A', 'hari' => 'Senin', 'jam_mulai' => '07:00', 'jam_berakhir' => '09:30', 'ruang' => 'Lab DB'],
            ['kode_mk' => 'MK005', 'kelas' => 'B', 'hari' => 'Senin', 'jam_mulai' => '09:30', 'jam_berakhir' => '12:00', 'ruang' => 'Lab DB'],
            ['kode_mk' => 'MK005', 'kelas' => 'C', 'hari' => 'Senin', 'jam_mulai' => '12:30', 'jam_berakhir' => '15:00', 'ruang' => 'Lab DB'],

            // IN233 Algoritma dan Struktur Data (Teori - MK006T)
            ['kode_mk' => 'MK006T', 'kelas' => 'A', 'hari' => 'Jumat', 'jam_mulai' => '12:30', 'jam_berakhir' => '15:00', 'ruang' => 'Lab ADV 2'],
            ['kode_mk' => 'MK006T', 'kelas' => 'B', 'hari' => 'Selasa', 'jam_mulai' => '09:30', 'jam_berakhir' => '12:00', 'ruang' => 'Lab ADV 2'],
            ['kode_mk' => 'MK006T', 'kelas' => 'C', 'hari' => 'Rabu', 'jam_mulai' => '09:30', 'jam_berakhir' => '12:00', 'ruang' => 'Lab ADV 3'],

            // IN233 Algoritma dan Struktur Data (Praktikum - MK006P)
            ['kode_mk' => 'MK006P', 'kelas' => 'A', 'hari' => 'Kamis', 'jam_mulai' => '15:00', 'jam_berakhir' => '17:00', 'ruang' => 'Lab ADV 2'],
            ['kode_mk' => 'MK006P', 'kelas' => 'B', 'hari' => 'Selasa', 'jam_mulai' => '12:30', 'jam_berakhir' => '14:30', 'ruang' => 'Lab ADV 2'],
            ['kode_mk' => 'MK006P', 'kelas' => 'C', 'hari' => 'Rabu', 'jam_mulai' => '12:30', 'jam_berakhir' => '14:30', 'ruang' => 'Lab ADV 3'],

            // IN234 Paradigma Pemrograman (Teori - MK001T)
            ['kode_mk' => 'MK001T', 'kelas' => 'A', 'hari' => 'Kamis', 'jam_mulai' => '09:30', 'jam_berakhir' => '12:00', 'ruang' => 'Lab ADV 4'],
            ['kode_mk' => 'MK001T', 'kelas' => 'B', 'hari' => 'Rabu', 'jam_mulai' => '09:30', 'jam_berakhir' => '12:00', 'ruang' => 'Lab ADV 4'],
            ['kode_mk' => 'MK001T', 'kelas' => 'C', 'hari' => 'Selasa', 'jam_mulai' => '09:30', 'jam_berakhir' => '12:00', 'ruang' => 'Lab ADV 4'],

            // IN234 Paradigma Pemrograman (Praktikum - MK001P)
            ['kode_mk' => 'MK001P', 'kelas' => 'A', 'hari' => 'Kamis', 'jam_mulai' => '12:30', 'jam_berakhir' => '14:30', 'ruang' => 'Lab ADV 4'],
            ['kode_mk' => 'MK001P', 'kelas' => 'B', 'hari' => 'Rabu', 'jam_mulai' => '12:30', 'jam_berakhir' => '14:30', 'ruang' => 'Lab ADV 4'],
            ['kode_mk' => 'MK001P', 'kelas' => 'C', 'hari' => 'Selasa', 'jam_mulai' => '12:30', 'jam_berakhir' => '14:30', 'ruang' => 'Lab ADV 4'],

            // IN237 Basis Data Lanjut (Teori - MK002T)
            ['kode_mk' => 'MK002T', 'kelas' => 'A', 'hari' => 'Jumat', 'jam_mulai' => '07:00', 'jam_berakhir' => '08:40', 'ruang' => 'Lab ADV 1'],
            ['kode_mk' => 'MK002T', 'kelas' => 'B', 'hari' => 'Senin', 'jam_mulai' => '15:00', 'jam_berakhir' => '16:40', 'ruang' => 'Lab ADV 2'],
            ['kode_mk' => 'MK002T', 'kelas' => 'C', 'hari' => 'Senin', 'jam_mulai' => '17:30', 'jam_berakhir' => '19:10', 'ruang' => 'Lab ADV 2'],

            // IN237 Basis Data Lanjut (Praktikum - MK002P)
            ['kode_mk' => 'MK002P', 'kelas' => 'A', 'hari' => 'Jumat', 'jam_mulai' => '13:00', 'jam_berakhir' => '15:00', 'ruang' => 'Lab ADV 1'],
            ['kode_mk' => 'MK002P', 'kelas' => 'B', 'hari' => 'Rabu', 'jam_mulai' => '15:00', 'jam_berakhir' => '17:00', 'ruang' => 'Lab ADV 1'],
            ['kode_mk' => 'MK002P', 'kelas' => 'C', 'hari' => 'Rabu', 'jam_mulai' => '17:00', 'jam_berakhir' => '19:00', 'ruang' => 'Lab ADV 1'],

            // IN243 Sistem Operasi Komputer (MK007)
            ['kode_mk' => 'MK007', 'kelas' => 'A', 'hari' => 'Jumat', 'jam_mulai' => '13:00', 'jam_berakhir' => '14:40', 'ruang' => 'Lab MMD'],
            ['kode_mk' => 'MK007', 'kelas' => 'B', 'hari' => 'Jumat', 'jam_mulai' => '15:00', 'jam_berakhir' => '16:40', 'ruang' => 'Lab MMD'],
            ['kode_mk' => 'MK007', 'kelas' => 'C', 'hari' => 'Jumat', 'jam_mulai' => '17:30', 'jam_berakhir' => '19:10', 'ruang' => 'Lab MMD'],
        ];

        // 4. Insert or Update
        $specificDosens = [
            'MK001T' => '0072201', // Tjatur - Paradigma Teori
            'MK001P' => '0072201', // Tjatur - Paradigma Praktikum
            'MK006T' => '0072206', // Wenny - Algoritma Teori
            'MK006P' => '0072206', // Wenny - Algoritma Praktikum
            'MK003'  => '0072205', // Maresha - RPL
            'MK002T' => '0072202', // Teddy - Basis Data Lanjut Teori
            'MK002P' => '0072202', // Teddy - Basis Data Lanjut Praktikum
            'MK007'  => '0072207', // Meliana - SOK
            'MK005'  => '0072204', // Andreas - Mat Diskrit
            'MK004'  => '0072203', // Erico - Tekmul (Assigned to separate)
        ];

        foreach ($schedules as $s) {
            if (array_key_exists($s['kode_mk'], $specificDosens)) {
                $nip = $specificDosens[$s['kode_mk']];
            } else {
                $nip = $dosens[$dosenIndex % count($dosens)];
                $dosenIndex++;
            }
            
            $roomId = $roomMap[$s['ruang']] ?? 'L8001'; // Fallback

            Perkuliahan::updateOrCreate(
                [
                    'kode_mk' => $s['kode_mk'],
                    'kelas' => $s['kelas'],
                    'tahun_ajaran' => '2025/2026 - Ganjil'
                ],
                [
                    'kode_ruangan' => $roomId,
                    'nip_dosen' => $nip,
                    'hari' => $s['hari'],
                    'jam_mulai' => $s['jam_mulai'],
                    'jam_berakhir' => $s['jam_berakhir'],
                ]
            );
        }
    }
}
