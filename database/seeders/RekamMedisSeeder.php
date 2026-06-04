<?php

namespace Database\Seeders;

use App\Models\Rekam_medis;
use Illuminate\Database\Seeder;

class RekamMedisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rekam_medis::create([
            'pasien_id' => 1,
        ]);
    }
}
