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
        Schema::table('admin', function (Blueprint $table) {
            $table->text('alamat')->nullable()->after('no_telepon');
        });

        Schema::table('dosen', function (Blueprint $table) {
            $table->text('alamat')->nullable()->after('no_telepon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            $table->dropColumn('alamat');
        });

        Schema::table('dosen', function (Blueprint $table) {
            $table->dropColumn('alamat');
        });
    }
};
