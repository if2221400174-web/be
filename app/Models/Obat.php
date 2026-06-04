<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obats';
    protected $fillable = [
        'nama_obat',
        'harga_obat'
        ];
    public function detailReseps(){
        return $this->hasMany(Detail_resep::class);
    }
}
