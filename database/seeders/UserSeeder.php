<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create(['name' => 'Rip', 'email' => 'rip@example.com']);
        User::create(['name' => 'Budi Santoso', 'email' => 'budi@example.com']);
        User::create(['name' => 'Citra Lestari', 'email' => 'citra@example.com']);
    }
}
