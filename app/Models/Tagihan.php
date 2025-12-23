<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';
    protected $fillable = ['npr','jenis','jumlah','status'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'npr');
    }
}
