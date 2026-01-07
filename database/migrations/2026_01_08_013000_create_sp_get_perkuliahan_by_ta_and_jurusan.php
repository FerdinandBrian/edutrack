<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_perkuliahan_by_ta_and_jurusan");

        $procedure = "
            CREATE PROCEDURE sp_get_perkuliahan_by_ta_and_jurusan(
                IN p_ta VARCHAR(255),
                IN p_jurusan VARCHAR(255)
            )
            BEGIN
                SELECT 
                    p.id_perkuliahan,
                    p.kode_mk,
                    m.nama_mk,
                    m.jurusan,
                    p.kelas,
                    p.hari,
                    p.jam_mulai,
                    p.jam_berakhir
                FROM perkuliahan p
                JOIN mata_kuliah m ON p.kode_mk = m.kode_mk
                WHERE p.tahun_ajaran = p_ta
                  AND m.jurusan = p_jurusan
                ORDER BY m.nama_mk ASC, p.kelas ASC;
            END
        ";
        
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_perkuliahan_by_ta_and_jurusan");
    }
};
