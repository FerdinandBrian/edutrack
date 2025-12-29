<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dkbs extends Model
{
    protected $table = 'dkbs';
    protected $fillable = ['nrp', 'kode_mk', 'id_perkuliahan', 'semester', 'status', 'tahun_ajaran'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nrp', 'nrp');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'kode_mk', 'kode_mk');
    }

    public function perkuliahan()
    {
        return $this->belongsTo(Perkuliahan::class, 'id_perkuliahan', 'id_perkuliahan');
    }
}
