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
     * No free games API is wired up yet, so these are curated by hand
     * (no cover image — the catalogue already falls back to a placeholder).
     */
    private const GAMES = [
        [
            'title' => 'The Legend of Zelda: Breath of the Wild',
            'synopsis' => 'Link wakes from a hundred-year slumber to a Hyrule overrun by monsters and awaits the return of Calamity Ganon. Explore a vast, open world however you like, solve shrine puzzles, and take on the kingdom\'s greatest threat at your own pace.',
            'release_date' => '2017-03-03',
            'genres' => ['Adventure', 'Action', 'Fantasy', 'RPG'],
            'platforms' => ['Nintendo Switch'],
        ],
        [
            'title' => 'Elden Ring',
            'synopsis' => 'A dark fantasy action RPG set in the Lands Between, shaped by the vision of Hidetaka Miyazaki and George R. R. Martin. Explore a vast open world, master brutal combat, and uncover the truth behind the shattering of the Elden Ring.',
            'release_date' => '2022-02-25',
            'genres' => ['RPG', 'Fantasy', 'Action'],
            'platforms' => ['PC', 'PlayStation 5', 'Xbox Series X/S'],
        ],
        [
            'title' => 'Hades',
            'synopsis' => 'A rogue-like dungeon crawler where you play as Zagreus, son of Hades, fighting to escape the Underworld. Every run reshapes the dungeon, deepens your relationships with the Olympians, and pushes the story forward.',
            'release_date' => '2020-09-17',
            'genres' => ['Action', 'RPG', 'Fantasy'],
            'platforms' => ['PC', 'Nintendo Switch', 'PlayStation 5', 'Xbox Series X/S'],
        ],
        [
            'title' => 'Stardew Valley',
            'synopsis' => 'You inherit your grandfather\'s old farm plot and set out to turn it into a thriving home. Grow crops, raise animals, fish, mine, and build relationships with the townsfolk of Pelican Town.',
            'release_date' => '2016-02-26',
            'genres' => ['Simulation', 'RPG'],
            'platforms' => ['PC', 'Nintendo Switch', 'iOS', 'Android', 'PlayStation 4', 'Xbox One'],
        ],
        [
            'title' => 'God of War Ragnarök',
            'synopsis' => 'Kratos and Atreus must journey to each of the Nine Realms in search of answers as Asgardian forces prepare for a prophesied battle that will end the world. Along the way they\'ll forge new bonds and face old grudges.',
            'release_date' => '2022-11-09',
            'genres' => ['Action', 'RPG', 'Fantasy'],
            'platforms' => ['PlayStation 5', 'PlayStation 4'],
        ],
        [
            'title' => "Baldur's Gate 3",
            'synopsis' => 'Gather your party and return to the Forgotten Realms in a story of fellowship and betrayal, sacrifice and survival, and the lure of absolute power. Mysterious abilities are awakening inside you, drawn from a mind flayer parasite planted in your brain.',
            'release_date' => '2023-08-03',
            'genres' => ['RPG', 'Fantasy', 'Strategy'],
            'platforms' => ['PC', 'PlayStation 5', 'Xbox Series X/S'],
        ],
        [
            'title' => 'Minecraft',
            'synopsis' => 'A sandbox game about placing blocks and going on adventures. Build anything you can imagine, survive the night, and explore procedurally generated worlds alone or with friends.',
            'release_date' => '2011-11-18',
            'genres' => ['Simulation', 'Adventure'],
            'platforms' => ['PC', 'PlayStation 4', 'PlayStation 5', 'Xbox One', 'Xbox Series X/S', 'Nintendo Switch', 'iOS', 'Android'],
        ],
        [
            'title' => 'Overwatch 2',
            'synopsis' => 'A free-to-play, team-based shooter set in an optimistic future. Choose your hero from an ever-growing roster and team up to battle it out in a variety of game modes across the globe.',
            'release_date' => '2022-10-04',
            'genres' => ['FPS', 'Action'],
            'platforms' => ['PC', 'PlayStation 5', 'Xbox Series X/S', 'Nintendo Switch'],
        ],
        [
            'title' => 'Animal Crossing: New Horizons',
            'synopsis' => 'Escape to a deserted island and turn it into a paradise of your own making. Collect resources, craft items, and build up your community one villager at a time, all at your own pace.',
            'release_date' => '2020-03-20',
            'genres' => ['Simulation'],
            'platforms' => ['Nintendo Switch'],
        ],
        [
            'title' => 'The Witcher 3: Wild Hunt',
            'synopsis' => 'As monster hunter Geralt of Rivia, search for your adopted daughter across a war-torn continent while contending with a otherworldly threat known as the Wild Hunt. A vast, reactive open world full of choices that matter.',
            'release_date' => '2015-05-19',
            'genres' => ['RPG', 'Fantasy', 'Adventure'],
            'platforms' => ['PC', 'PlayStation 4', 'PlayStation 5', 'Xbox One', 'Xbox Series X/S', 'Nintendo Switch'],
        ],
        [
            'title' => 'Celeste',
            'synopsis' => 'Help Madeline survive her inner demons on her journey to the top of Celeste Mountain, in this super-tight platformer from the creators of TowerFall.',
            'release_date' => '2018-01-25',
            'genres' => ['Adventure', 'Puzzle'],
            'platforms' => ['PC', 'PlayStation 4', 'Xbox One', 'Nintendo Switch'],
        ],
        [
            'title' => 'Portal 2',
            'synopsis' => 'Wake up and try to escape from Aperture Science using a device that creates linked portals. A first-person puzzle game with dark comedy, mind-bending physics, and a memorable cast of AI companions.',
            'release_date' => '2011-04-19',
            'genres' => ['Puzzle', 'Sci-Fi'],
            'platforms' => ['PC'],
        ],
    ];

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
        }

        foreach (self::GAMES as $game) {
            $title = Title::create([
                'api_source' => 'manual',
                'type' => 'game',
                'title' => $game['title'],
                'slug' => Str::slug($game['title']),
                'synopsis' => $game['synopsis'],
                'release_date' => $game['release_date'],
                'status' => 'accepted',
            ]);

            $genreIds = Genre::whereIn('name', $game['genres'])->get()->unique('name')->pluck('id');
            $title->genres()->sync($genreIds);

            $platformIds = Platform::whereIn('name', $game['platforms'])->pluck('id');
            $title->platforms()->sync($platformIds);
        }
    }
}
