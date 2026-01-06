<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';
    protected $primaryKey = 'kode_mk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'jurusan',
        'sks',
        'semester'
    ];

    public function perkuliahan()
    {
        return $this->hasMany(Perkuliahan::class, 'kode_mk', 'kode_mk');
    }
}
