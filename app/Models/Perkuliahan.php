<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perkuliahan extends Model
{
    protected $table = 'perkuliahan';
    protected $primaryKey = 'id_perkuliahan';

    protected $fillable = [
        'kode_ruangan',
        'nip_dosen',
        'kode_mk',
        'kelas',
        'hari',
        'jam_mulai',
        'jam_berakhir',
        'tahun_ajaran'
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'kode_mk', 'kode_mk');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'nip_dosen', 'nip');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'kode_ruangan', 'kode_ruangan');
    }
}
