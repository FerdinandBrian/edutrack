<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admin';
    protected $primaryKey = 'kode_admin'; 
    public $incrementing = false;
    protected $keyType = 'string';

    // kolom yang bisa diisi massal
    protected $fillable = [
        'kode_admin',
        'user_id',
        'nama',
        'email',
        'jenis_kelamin',
        'tanggal_lahir',
        'no_telepon',
        'alamat',
    ];

    // kolom yang disembunyikan
    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}