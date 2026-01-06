<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $procedure = "
            CREATE PROCEDURE sp_get_va(IN student_nrp VARCHAR(50))
            BEGIN
                SELECT CONCAT('2911', student_nrp) AS va;
            END;
        ";

        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_va");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_va");
    }
};
