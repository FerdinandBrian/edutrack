<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosen';
    protected $primaryKey = 'nidn';
    public $incrementing = false;

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'nidn');
    }
}

