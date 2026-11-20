<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun contoh dengan password tetap hanya untuk pengembangan.
        if (app()->environment('production')) {
            return;
        }

        // ADMIN
        User::firstOrCreate(['email' => 'admin@simasta.test'], [
            'name' => 'Admin SIMASTA',
            'email' => 'admin@simasta.test',
            'password' => Hash::make('admin12345'),
            'role' => 'admin',
        ]);

        // PEMBIMBING
        User::firstOrCreate(['email' => 'pembimbing@simasta.test'], [
            'name' => 'Pembimbing Lapangan',
            'email' => 'pembimbing@simasta.test',
            'password' => Hash::make('pembimbing123'),
            'role' => 'pembimbing',
        ]);
    }
}
