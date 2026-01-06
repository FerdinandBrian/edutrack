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
        Schema::table('tagihan', function (Blueprint $table) {
            $table->date('batas_pembayaran')->nullable()->after('jumlah');
            $table->integer('tipe_pembayaran')->nullable()->after('batas_pembayaran'); // 1 or 3
            $table->integer('cicilan_ke')->nullable()->after('tipe_pembayaran'); // 1, 2, or 3
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn(['batas_pembayaran', 'tipe_pembayaran', 'cicilan_ke']);
        });
    }
};
