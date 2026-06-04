<?php

namespace Database\Seeders;

use App\Models\Detail_resep;
use Illuminate\Database\Seeder;

class DetailResepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            Detail_resep::create([
                'resep_id' => 1,
                'obat_id' => 1,
                'aturan_pakai' => '3x sehari setelah makan'
            ]);
    }
}
