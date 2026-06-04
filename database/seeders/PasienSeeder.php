<?php

namespace Database\Seeders;

use App\Models\Pasien;
use Illuminate\Database\Seeder;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pasien::create([
            'nama' => 'John Doe',
            'kode_rekammedis' => 'PUMDR-001',
            'alamat' => 'Guluk-guluk, Sumenep',
            'tanggal_lahir' => '1993-05-15',
            'jenis_kelamin' => 'Laki-laki',
        ]);
    }
}

