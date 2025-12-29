<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Mahasiswa extends Authenticatable
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'nrp';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nrp',
        'user_id',
        'nama',
        'jurusan',
        'email',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'no_telepon'
    ];

    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
