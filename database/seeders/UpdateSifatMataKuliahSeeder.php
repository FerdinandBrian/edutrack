<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateSifatMataKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // List of Elective Courses (Pilihan)
        $pilihanCodes = [
            // DKV
            'DKVE001202564',
            'DKVE002202564',
            'DKVE003202564',
            'DKVE004202564',
            'DKVE005202564',
            'DKVE006202564',
            'DKVE007202564',
            'DKVE008202564',
            'DKVE009202564',
            'DKVE010202564',
            'DKVI001202564',
            'DKVI002202564',

            // Seni Rupa Murni
            'SH212',
            'SH313',
            'SH315',
            'AH302',
            'SH314',
            'SH316',
            'SH413',
            'SH415',

            // Arsitektur
            'AR321',
            'AR323',
            'AR325',
            'AR327',
            'AR421',
            'AR423',
            'AR425',
            'AR427',
            'AR429',

            // Fashion Design
            'AH301',
            'AH302SRD',
            'AH303',
            'AH304',
            'AH305',
        ];

        // Reset all to Wajib first (optional but safer if running multiple times)
        DB::table('mata_kuliah')->update(['sifat' => 'Wajib']);

        // Update Pilihan
        DB::table('mata_kuliah')
            ->whereIn('kode_mk', $pilihanCodes)
            ->update(['sifat' => 'Pilihan']);
            
        $count = DB::table('mata_kuliah')->where('sifat', 'Pilihan')->count();
        $this->command->info("Updated {$count} courses to 'Pilihan'.");
    }
}
