<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            'PC',
            'PlayStation 5',
            'PlayStation 4',
            'Xbox Series X/S',
            'Xbox One',
            'Nintendo Switch',
            'iOS',
            'Android',
        ];

        foreach ($platforms as $name) {
            Platform::create([
                'name' => $name,
            ]);
        }
    }
}
