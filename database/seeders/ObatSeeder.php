<?php

namespace Database\Seeders;

use App\Models\Obat;
use Illuminate\Database\Seeder;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Obat::create([
            'nama_obat' => 'Paracetamol',
            'harga_obat' => '5000',
        ]);
        Obat::create([
            'nama_obat' => 'Amoxicillin',
            'harga_obat' => '10000',
        ]);
        Obat::create([
            'nama_obat' => 'Ibuprofen',
            'harga_obat' => '8000',
        ]);
    }
}
