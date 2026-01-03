<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi';
    protected $fillable = ['nrp','jadwal_id','tanggal','status','keterangan'];

    public function mahasiswa()
    {
        // support both possible column names (nrp or legacy npr)
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

    public function jadwal()
    {
        // presensi.jadwal_id refers to perkuliahan.id_perkuliahan
        // We use 'jadwal' name to keep compatibility with existing code calling ->jadwal
        return $this->belongsTo(Perkuliahan::class, 'jadwal_id', 'id_perkuliahan');
    }
}
