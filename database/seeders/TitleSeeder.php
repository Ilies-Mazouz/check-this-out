<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Platform;
use App\Models\Title;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assetsDir = database_path('seeders/assets/titles');
        $data = json_decode(file_get_contents(database_path('seeders/assets/titles_data.json')), true);

        foreach ($data as $entry) {
            $imageContents = file_get_contents("$assetsDir/{$entry['image']}");
            $storedPath = 'titles/'.$entry['image'];
            Storage::disk('public')->put($storedPath, $imageContents);

            $title = Title::create([
                'api_source' => $entry['api_source'],
                'api_id' => $entry['api_id'],
                'source_slug' => $entry['source_slug'] ?? null,
                'type' => $entry['type'],
                'title' => $entry['title'],
                'slug' => Str::slug($entry['title']),
                'synopsis' => $entry['synopsis'],
                'cover_image' => $storedPath,
                'release_date' => $entry['release_date'],
                'status' => 'accepted',
            ]);

            // Genre names aren't unique across types (e.g. "Horror" exists
            // for both 'all' and 'game'), so dedupe by name before syncing.
            $genreIds = Genre::whereIn('name', $entry['genres'] ?? [])->get()->unique('name')->pluck('id');
            $title->genres()->sync($genreIds);

            if (! empty($entry['platforms'])) {
                $platformIds = Platform::whereIn('name', $entry['platforms'])->pluck('id');
                $title->platforms()->sync($platformIds);
            }
        }
    }
}
