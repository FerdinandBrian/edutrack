<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perkuliahan;
use App\Models\Dkbs;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PresensiSeeder extends Seeder
{
    public function run()
    {
        // 1. Initial Setup
        $startDate = Carbon::create(2025, 2, 17); // Start 17 Feb 2025
        $weeks = 16; // 1 semester usually 16 meetings
        
        $dayMap = [
            'Senin' => Carbon::MONDAY,
            'Selasa' => Carbon::TUESDAY,
            'Rabu' => Carbon::WEDNESDAY,
            'Kamis' => Carbon::THURSDAY,
            'Jumat' => Carbon::FRIDAY,
            'Sabtu' => Carbon::SATURDAY,
            'Minggu' => Carbon::SUNDAY,
        ];

        // Clear existing presensi to avoid duplicates during dev
        // Presensi::truncate(); // Optional: decided not to truncate to be safe, but use firstOrCreate

        $perkuliahans = Perkuliahan::all();

        foreach ($perkuliahans as $jadwal) {
            $dayInt = $dayMap[$jadwal->hari] ?? null;
            if (!$dayInt) continue;

            // Get students enrolled in this class
            $students = Dkbs::where('id_perkuliahan', $jadwal->id_perkuliahan)->pluck('nrp');

            if ($students->isEmpty()) continue;

            // Find first occurrence of this day ON or AFTER start date
            $currentDate = $startDate->copy();
            while ($currentDate->dayOfWeek !== $dayInt) {
                $currentDate->addDay();
            }

            // Generate 16 sessions
            for ($i = 0; $i < $weeks; $i++) {
                $dateString = $currentDate->format('Y-m-d');
                
                foreach ($students as $nrp) {
                    Presensi::firstOrCreate(
                        [
                            'nrp' => $nrp,
                            'jadwal_id' => $jadwal->id_perkuliahan,
                            'tanggal' => $dateString
                        ],
                        [
                            'status' => 'Hadir', // Default template status
                            'keterangan' => null
                        ]
                    );
                }

                $currentDate->addWeek();
            }
        }
    }
}
