<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi';
    protected $fillable = ['npr','tanggal','status'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'npr');
    }
}
