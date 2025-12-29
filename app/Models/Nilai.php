<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $table = 'nilai';
    protected $fillable = [
        'nrp',
        'kode_mk',
        'p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7',
        'uts',
        'p9', 'p10', 'p11', 'p12', 'p13', 'p14', 'p15',
        'uas',
        'nilai_total',
        'nilai_akhir'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nrp', 'nrp');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'kode_mk', 'kode_mk');
    }

    /**
     * Logic to calculate Grade (A, B, C, etc) from total score
     */
    public static function calculateGrade($total)
    {
        if ($total >= 85) return 'A';
        if ($total >= 75) return 'B';
        if ($total >= 60) return 'C';
        if ($total >= 45) return 'D';
        return 'E';
    }
}
