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
        $procedure = "
            CREATE PROCEDURE sp_get_matkul_by_jurusan(IN p_jurusan VARCHAR(255))
            BEGIN
                SELECT * 
                FROM mata_kuliah 
                WHERE jurusan = p_jurusan 
                ORDER BY semester ASC, nama_mk ASC;
            END;
        ";

        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_matkul_by_jurusan");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_matkul_by_jurusan");
    }
};
