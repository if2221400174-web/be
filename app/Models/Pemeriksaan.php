<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    protected $fillable = [
        'tanggal_pemeriksaan',
        'keluhan',
        'diagnosa',
        'catatan',
        'rekam_medis_id',
        'user_id'
    ];

    public function rekam_medis()
{
    return $this->belongsTo(Rekam_medis::class, 'rekam_medis_id');
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function resep(){
        return $this->hasMany(Resep::class);
    }

    public function transaksi(){
        return $this->hasOne(Transaksi::class);
    }
}
