<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'nrp'; // kalau pk kamu nrp
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nrp',
        'nama',
        'email',
        'password',
        'id_role'
    ];

    protected $hidden = [
        'password'
    ];

    // User.php
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id', 'id'); // user_id di mahasiswa -> id di users
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'user_id', 'id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id', 'id');
    }

}