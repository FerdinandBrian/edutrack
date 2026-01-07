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
        Schema::table('admin', function (Blueprint $table) {
            $table->enum('admin_level', ['super', 'second'])->default('second')->after('jenis_kelamin');
        });

        // Set existing admin (ADM01) as super admin
        DB::table('admin')->where('kode_admin', 'ADM01')->update(['admin_level' => 'super']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            $table->dropColumn('admin_level');
        });
    }
};
