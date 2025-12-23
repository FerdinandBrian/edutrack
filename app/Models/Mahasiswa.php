<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'npr';
    public $incrementing = false;
    protected $keyType = 'string';

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'npr');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'npr');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'npr');
    }
}
