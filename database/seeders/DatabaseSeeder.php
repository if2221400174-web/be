<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this ->call([
            PasienSeeder::class,
            RekamMedisSeeder::class,
            UserSeeder::class,
            PemeriksaanSeeder::class,
            ObatSeeder::class,
            ResepSeeder::class,
            DetailResepSeeder::class,
            TransaksiSeeder::class

        ]);
    }
}
