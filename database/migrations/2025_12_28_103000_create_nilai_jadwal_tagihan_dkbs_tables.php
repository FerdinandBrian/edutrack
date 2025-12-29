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
        if (!Schema::hasTable('nilai')) {
            Schema::create('nilai', function (Blueprint $table) {
                $table->id();
                $table->string('nrp')->index();
                $table->string('kode_mk');
                $table->decimal('nilai',5,2);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('jadwal')) {
            Schema::create('jadwal', function (Blueprint $table) {
                $table->id();
                $table->string('kode_mk');
                $table->string('nidn')->nullable();
                $table->string('hari')->nullable();
                $table->string('jam')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tagihan')) {
            Schema::create('tagihan', function (Blueprint $table) {
                $table->id();
                $table->string('nrp')->index();
                $table->string('jenis');
                $table->decimal('jumlah',12,2);
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('dkbs')) {
            Schema::create('dkbs', function (Blueprint $table) {
                $table->id();
                $table->string('nrp')->index();
                $table->string('kode_mk');
                $table->string('semester')->nullable();
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dkbs');
        Schema::dropIfExists('tagihan');
        Schema::dropIfExists('jadwal');
        Schema::dropIfExists('nilai');
    }
};
