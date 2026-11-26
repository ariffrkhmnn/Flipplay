<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;

// Data awal sama dengan seedLibrary() di Modul 3.
class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['title' => "Frieren: Beyond Journey's End", 'type' => 'anime', 'genre' => 'Fantasy', 'year' => 2023, 'length' => 28, 'rating' => 9.3],
            ['title' => 'Jujutsu Kaisen', 'type' => 'anime', 'genre' => 'Action', 'year' => 2020, 'length' => 24, 'rating' => 8.7],
            ['title' => 'Crash Landing on You', 'type' => 'drama', 'genre' => 'Romance', 'year' => 2019, 'length' => 16, 'rating' => 8.7],
            ['title' => 'Vagabond', 'type' => 'manga', 'genre' => 'Action', 'year' => 1998, 'length' => 327, 'rating' => 9.2],
            ['title' => 'Chainsaw Man', 'type' => 'manga', 'genre' => 'Action', 'year' => 2018, 'length' => 97, 'rating' => 8.6, 'is_available' => false], // segera hadir
            ['title' => 'Spice and Wolf', 'type' => 'light_novel', 'genre' => 'Fantasy', 'year' => 2006, 'length' => 17, 'rating' => 8.4],
        ];

        foreach ($data as $item) {
            Media::create($item);
        }

        // Contoh koleksi awal: user pertama sudah punya 2 media (Frieren & Vagabond)
        User::first()?->media()->attach(Media::whereIn('title', ["Frieren: Beyond Journey's End", 'Vagabond'])->pluck('id'));
    }
}
