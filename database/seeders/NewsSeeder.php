<?php

namespace Database\Seeders;

use App\Models\NewsArticle;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('username', 'admin')->firstOrFail();

        $articles = [
            [
                'title' => 'Welcome to Check This Out',
                'body' => "Check This Out is now live! Track movies, series, anime and games you love, rate and review titles, and discover what the community is watching next.\n\nCreate an account, build your watchlist, and check back here for updates.",
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Now Accepting Community Submissions',
                'body' => "Can't find a title in the catalogue? Submit it yourself from the catalogue page. Movies and series can be pulled straight from TMDB, and anime from AniList — just search, pick the right result, and submit for review.\n\nAn admin will approve or reject your submission, and you'll get a notification either way.",
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Pick Your Theme',
                'body' => "Check This Out ships with two visual themes, Neon Arcade and Ember, each available in dark or light mode. Open the palette icon in the navbar to switch between them — your choice is saved to your account.",
                'published_at' => now()->subDay(),
            ],
            [
                'title' => 'Sequel to Fan-Favourite Sci-Fi Series Officially Greenlit',
                'body' => "After months of speculation, the studio has confirmed a fourth season is officially in production, with a writers' room already assembled. No release window has been announced yet, but early concept art teases a darker tone than previous seasons.\n\nFans are already theorizing about where the story goes next after that finale.",
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Long-Awaited RPG Sequel Finally Gets a Release Date',
                'body' => "After three years of near-silence, the developer has confirmed a release date for the sequel to their breakout RPG hit. A new trailer showcased overhauled combat, a larger open world, and returning characters from the original game.\n\nPre-orders open next month across all major platforms.",
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Popular Anime Adaptation Delayed Amid Production Issues',
                'body' => "The studio behind one of the season's most anticipated adaptations has announced a delay, citing the need for \"additional time to ensure animation quality.\" The series was originally slated for this season but will now air later in the year.\n\nThis isn't the first delay for the project, and fans have had mixed reactions online.",
                'published_at' => now()->subDays(6),
            ],
            [
                'title' => 'Streaming Series Cancelled After Three Seasons',
                'body' => "Despite a dedicated fanbase, the platform has confirmed the show will not be renewed for a fourth season, citing viewership numbers that didn't meet targets. The creators say they have a wrap-up plan in mind if a new home for the series is found.\n\nA petition to save the show has already gathered thousands of signatures.",
                'published_at' => now()->subDays(4),
            ],
            [
                'title' => 'Major Update Overhauls Combat System in Hit Multiplayer Game',
                'body' => "The latest patch introduces a full rework of the combat system, new balance changes across the roster, and a fresh ranked season. The developers say the changes are aimed at making matches feel \"more readable and less chaotic\" at high skill levels.\n\nEarly community feedback has been largely positive, though a few changes remain controversial.",
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Cult Classic Film Getting a Modern Reboot',
                'body' => "A new director has been attached to a reboot of the cult classic, with the studio promising to \"honour the original while bringing it to a new generation.\" Casting details haven't been revealed yet, but a script is reportedly already in its second draft.\n\nReactions from longtime fans have been cautiously optimistic.",
                'published_at' => now()->subDays(2),
            ],
        ];

        // These are illustrative/example articles (either about the site
        // itself or generic fictional entertainment news), not real
        // published pieces — so unlike RSS-imported articles, there is no
        // genuine photo that actually belongs to any of them. Deliberately
        // left without a cover_image; the themed placeholder covers it.
        foreach ($articles as $article) {
            NewsArticle::create([
                'user_id' => $admin->id,
                'title' => $article['title'],
                'slug' => Str::slug($article['title']),
                'body' => $article['body'],
                'published_at' => $article['published_at'],
            ]);
        }
    }
}
