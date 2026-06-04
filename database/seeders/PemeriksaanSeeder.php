<?php

namespace Database\Seeders;

use App\Models\Pemeriksaan;
use Illuminate\Database\Seeder;

class PemeriksaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            Pemeriksaan::create([
                'tanggal_pemeriksaan' => '2024-05-01',
                'keluhan' => 'Demam tinggi dan batuk',
                'diagnosa' => 'Infeksi Saluran Pernapasan Atas',
                'catatan' => 'Pasien disarankan untuk istirahat dan minum banyak air.',
                'rekam_medis_id' => 1,
                'user_id' => 1
            ]);
    }
}
