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
        Schema::table('nilai', function (Blueprint $table) {
            // Drop old column if exists
            if (Schema::hasColumn('nilai', 'kat')) {
                $table->dropColumn('kat');
            }

            // Add pertemuan columns
            $table->float('p1')->default(0)->after('kode_mk');
            $table->float('p2')->default(0)->after('p1');
            $table->float('p3')->default(0)->after('p2');
            $table->float('p4')->default(0)->after('p3');
            $table->float('p5')->default(0)->after('p4');
            $table->float('p6')->default(0)->after('p5');
            $table->float('p7')->default(0)->after('p6');
            // uts (p8) is already there, but let's ensure it's float
            $table->float('uts')->default(0)->change();
            
            $table->float('p9')->default(0)->after('uts');
            $table->float('p10')->default(0)->after('p9');
            $table->float('p11')->default(0)->after('p10');
            $table->float('p12')->default(0)->after('p11');
            $table->float('p13')->default(0)->after('p12');
            $table->float('p14')->default(0)->after('p13');
            $table->float('p15')->default(0)->after('p14');
            // uas (p16) is already there
            $table->float('uas')->default(0)->change();

            // Total Score (Numerical)
            $table->float('nilai_total')->default(0)->after('uas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $cols = ['p1','p2','p3','p4','p5','p6','p7','p9','p10','p11','p12','p13','p14','p15','nilai_total'];
            foreach($cols as $col) {
                if (Schema::hasColumn('nilai', $col)) {
                    $table->dropColumn($col);
                }
            }
            $table->float('kat')->default(0);
        });
    }
};
