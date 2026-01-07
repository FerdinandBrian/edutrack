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
        // Drop safely
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_matkul_by_jurusan");

        // Create with explicit delimiter handling simulation if needed, but standard string is usually fine.
        // We will try a slightly different formatting just in case.
        $procedure = "
            CREATE PROCEDURE sp_get_matkul_by_jurusan(IN p_jurusan VARCHAR(255))
            BEGIN
                SELECT * 
                FROM mata_kuliah 
                WHERE jurusan = p_jurusan COLLATE utf8mb4_unicode_ci
                ORDER BY semester ASC, nama_mk ASC;
            END
        ";
        
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
