<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'foto' => null,
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);

        User::create([
            'username' => 'dokter',
            'foto' => null,
            'email' => 'dokter@example.com',
            'password' => bcrypt('dokter123'),
            'role' => 'dokter'
        ]);
    }
}
