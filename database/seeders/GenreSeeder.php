<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'all' => [
                'Action',
                'Adventure',
                'Comedy',
                'Drama',
                'Fantasy',
                'Horror',
                'Mystery',
                'Romance',
                'Sci-Fi',
                'Thriller',
            ],
            'game' => [
                'RPG',
                'FPS',
                'Strategy',
                'Sports',
                'Puzzle',
                'Fighting',
                'Simulation',
                'Horror',
            ],
            'anime' => [
                'Shounen',
                'Shoujo',
                'Seinen',
                'Isekai',
                'Mecha',
                'Slice of Life',
            ],
            'movie' => [
                'Documentary',
                'Animation',
            ],
            'series' => [
                'Reality',
                'Talk Show',
            ],
        ];

        foreach ($genres as $type => $items) {
            foreach ($items as $name) {
                Genre::create([
                    'name' => $name,
                    'type' => $type,
                ]);
            }
        }
    }
}
