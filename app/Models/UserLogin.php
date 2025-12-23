<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    protected $table = 'userlogin';
    protected $primaryKey = 'nrp';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['nrp', 'password', 'idRole'];

    protected $hidden = ['password'];

    public function role() {
        return $this->belongsTo(Role::class, 'idRole');
    }
}
