<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pasien extends Model
{
    protected $table = 'pasien';

    protected $fillable = [
        'nama',
        'kode_rekammedis',
        'alamat',
        'no_wa',
        'tanggal_lahir',
        'jenis_kelamin',
    ];

    //agar field umur otomatis muncul di JSON
    protected $appends = ['umur'];

    //untuk menghitung umur
    public function getUmurAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    public function rekamMedis(){
        return $this->hasOne(Rekam_medis::class);
    }

    // membuat rekam medis secara otomatis saat pasien dibuat
    protected static function booted()
    {
        static::created(function ($pasien) {
            if (!$pasien->rekamMedis) {
                $pasien->rekamMedis()->create([]);
            }
        });
    }
}
