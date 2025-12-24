<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Dosen extends Authenticatable
{
    protected $table = 'dosen';
    protected $primaryKey = 'nidn'; // bisa disesuaikan primary key
    public $incrementing = false;
    protected $keyType = 'string';

    // kolom yang bisa diisi massal
    protected $fillable = [
        'nip',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'email',
        'no_telepon',
        'fakultas',
        'id_role',
        'password',
    ];

    // kolom yang disembunyikan
    protected $hidden = ['password'];

    // relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}