<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admin';
    protected $primaryKey = 'id'; // gunakan 'id' jika auto increment
    public $incrementing = true;  // true jika pakai auto increment
    protected $keyType = 'int';

    // kolom yang bisa diisi massal
    protected $fillable = [
        'kode_admin',
        'nama',
        'email',
        'password',
        'jenis_kelamin',
        'tanggal_lahir',
        'no_telepon',
        'id_role',
    ];

    // kolom yang disembunyikan
    protected $hidden = ['password'];

    // relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}