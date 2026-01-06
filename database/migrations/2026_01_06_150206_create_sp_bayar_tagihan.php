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
            CREATE PROCEDURE sp_bayar_tagihan(IN tagihan_id INT)
            BEGIN
                UPDATE tagihan SET status = 'Lunas', updated_at = NOW() WHERE id = tagihan_id;
            END;
        ";

        DB::unprepared("DROP PROCEDURE IF EXISTS sp_bayar_tagihan");
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_bayar_tagihan");
    }
};
