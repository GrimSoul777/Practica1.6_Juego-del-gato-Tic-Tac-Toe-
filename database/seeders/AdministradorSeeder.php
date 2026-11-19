<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdministradorSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => 'Admin123',
                'rol' => 'Administrador',
            ]
        );
    }
}