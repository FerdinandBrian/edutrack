<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $fillable = ['kode_mk','nidn','hari','jam'];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'nidn');
    }
}
