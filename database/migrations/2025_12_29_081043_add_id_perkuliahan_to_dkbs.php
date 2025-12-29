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
        Schema::table('dkbs', function (Blueprint $table) {
            $table->unsignedBigInteger('id_perkuliahan')->after('kode_mk')->nullable();
            
            // Optional: for better visualization in migrations
            // $table->foreign('id_perkuliahan')->references('id_perkuliahan')->on('perkuliahan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dkbs', function (Blueprint $table) {
            $table->dropColumn('id_perkuliahan');
        });
    }
};
