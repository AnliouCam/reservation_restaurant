<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@restaurant.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Reception',
            'email' => 'reception@restaurant.com',
            'password' => Hash::make('password'),
            'role' => 'reception',
        ]);
    }
}
