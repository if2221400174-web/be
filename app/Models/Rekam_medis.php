<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekam_medis extends Model
{
    protected $table = 'rekam_medis';

    protected $fillable = [
        'pasien_id',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }
    public function pemeriksaan()
    {
        return $this->hasMany(Pemeriksaan::class, 'rekam_medis_id');
    }
}
