<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Dosen extends Authenticatable
{
    protected $table = 'dosen';
    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $keyType = 'string';

    // kolom yang bisa diisi massal
    protected $fillable = [
        'nip',
        'user_id',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'email',
        'no_telepon',
        'fakultas',
        'jurusan',
        'alamat'
    ];

    // kolom yang disembunyikan
    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}