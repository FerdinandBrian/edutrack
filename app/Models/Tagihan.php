<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';
    protected $fillable = ['nrp','jenis','jumlah','status', 'batas_pembayaran', 'tipe_pembayaran', 'cicilan_ke'];

    public function mahasiswa()
    {
        // support both column names (nrp or legacy npr)
        if (\Illuminate\Support\Facades\Schema::hasColumn($this->getTable(), 'nrp')) {
            return $this->belongsTo(Mahasiswa::class, 'nrp', 'nrp');
        }
        return $this->belongsTo(Mahasiswa::class, 'npr', 'nrp');
    }

    public function getNrpAttribute()
    {
        return $this->attributes['nrp'] ?? ($this->attributes['npr'] ?? null);
    }

    public function setNrpAttribute($value)
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn($this->getTable(), 'nrp')) {
            $this->attributes['nrp'] = $value;
        } else {
            $this->attributes['npr'] = $value;
        }
    }
}
