<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail_resep extends Model
{
    protected $table = 'detail_reseps';

    protected $fillable = [
        'resep_id',
        'obat_id',
        'aturan_pakai',
    ];

    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
