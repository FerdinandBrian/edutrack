<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'userlogin';
    protected $primaryKey = 'nrp';
    public $timestamps = false;

    protected $fillable = [
        'nrp',
        'password',
        'idRole'
    ];

    protected $hidden = [
        'password',
    ];
}
