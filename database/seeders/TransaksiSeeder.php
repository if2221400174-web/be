<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use Illuminate\Database\Seeder;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            Transaksi::create([
                'jasa_medis' => 30000,
                'total_tarif' => 50000,
                'pemeriksaan_id' => 1,
            ]);
    }
}
