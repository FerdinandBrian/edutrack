<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password'
    ];

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id', 'id');
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'user_id', 'id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id', 'id');
    }

    public function getIdentifierAttribute()
    {
        return match($this->role) {
            'mahasiswa' => $this->mahasiswa?->nrp,
            'dosen' => $this->dosen?->nip,
            'admin' => $this->admin?->kode_admin,
            default => 'N/A'
        };
    }

}