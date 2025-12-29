<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            if (!Schema::hasColumn('mata_kuliah', 'hari')) {
                $table->string('hari', 10)->after('sks')->nullable();
            }
            if (!Schema::hasColumn('mata_kuliah', 'jam_mulai')) {
                $table->time('jam_mulai')->after('hari')->nullable();
            }
            if (!Schema::hasColumn('mata_kuliah', 'jam_berakhir')) {
                $table->time('jam_berakhir')->after('jam_mulai')->nullable();
            }
            if (!Schema::hasColumn('mata_kuliah', 'kode_ruangan')) {
                $table->string('kode_ruangan')->after('semester')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropColumn(['hari', 'jam_mulai', 'jam_berakhir', 'kode_ruangan']);
        });
    }
};
