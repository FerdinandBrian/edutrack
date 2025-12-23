<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $table = 'nilai';
    protected $fillable = ['npr','kode_mk','nilai'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'npr');
    }
}
