<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting: User dulu, baru Media (karena MediaSeeder mengisi koleksi user pertama)
        $this->call([
            UserSeeder::class,
            MediaSeeder::class,
        ]);
    }
}
