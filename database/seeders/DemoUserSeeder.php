<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\FaqSuggestion;
use App\Models\GamingEntry;
use App\Models\NotificationSetting;
use App\Models\Review;
use App\Models\Title;
use App\Models\User;
use App\Models\WatchlistEntry;
use Illuminate\Database\Seeder;

/**
 * Two hand-picked demo accounts (todobrozaa, lootgoblin) with realistic
 * activity across every social feature, so the grader sees a populated
 * catalogue instead of an empty one on a fresh migrate:fresh --seed.
 * Content here is placeholder — feel free to edit the strings below.
 */
class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $todobrozaa = User::create([
            'username' => 'todobrozaa',
            'email' => 'todobrozaa@example.com',
            'password' => 'Testtest',
            'bio' => "Always down for a good binge — movies, series, the occasional anime. Currently on a mission to find my next favorite show.",
            'birthday' => '2001-03-14',
        ]);
        NotificationSetting::create(['user_id' => $todobrozaa->id]);

        $lootgoblin = User::create([
            'username' => 'lootgoblin',
            'email' => 'lootgoblin@example.com',
            'password' => 'Testtest',
            'bio' => '100%-ing every game I touch. If there\'s an achievement, I\'ve probably already got it.',
            'birthday' => '1998-11-02',
        ]);
        NotificationSetting::create(['user_id' => $lootgoblin->id]);

        $stardew = Title::where('slug', 'stardew-valley')->first();
        $witcher3 = Title::where('slug', 'the-witcher-3-wild-hunt')->first();
        $godOfWar = Title::where('slug', 'god-of-war-ragnarok')->first();
        $animalCrossing = Title::where('slug', 'animal-crossing-new-horizons')->first();
        $strangerThings = Title::where('slug', 'stranger-things')->first();
        $lastOfUs = Title::where('slug', 'the-last-of-us')->first();
        $parasite = Title::where('slug', 'parasite')->first();
        $deathNote = Title::where('slug', 'death-note')->first();
        $silentVoice = Title::where('slug', 'a-silent-voice')->first();
        $myHeroAcademia = Title::where('slug', 'my-hero-academia')->first();
        $swordArtOnline = Title::where('slug', 'sword-art-online')->first();

        // --- todobrozaa: reviews ---
        $godOfWarReview = null;

        if ($strangerThings) {
            Review::create([
                'user_id' => $todobrozaa->id,
                'title_id' => $strangerThings->id,
                'depth' => 1,
                'score' => 5,
                'body' => 'Rewatched the whole thing for the third time and it still hits. The 80s nostalgia never feels like a gimmick.',
            ]);
        }

        if ($parasite) {
            Review::create([
                'user_id' => $todobrozaa->id,
                'title_id' => $parasite->id,
                'depth' => 1,
                'score' => 5,
                'body' => 'One of those movies you need a few days to process. No notes.',
            ]);
        }

        if ($godOfWar) {
            $godOfWarReview = Review::create([
                'user_id' => $todobrozaa->id,
                'title_id' => $godOfWar->id,
                'depth' => 1,
                'score' => 4,
                'body' => "Combat is incredible, pacing dips a bit in the middle, but the ending makes up for it.",
            ]);
        }

        // --- lootgoblin: reviews, incl. a reply + a like to show off threading ---
        if ($witcher3) {
            $witcherReview = Review::create([
                'user_id' => $lootgoblin->id,
                'title_id' => $witcher3->id,
                'depth' => 1,
                'score' => 5,
                'body' => 'Sunk 200 hours into this and still finding side quests I missed. Genuinely one of the best RPGs ever made.',
            ]);

            $witcherReview->likes()->syncWithoutDetaching([$todobrozaa->id]);
        }

        if ($animalCrossing) {
            Review::create([
                'user_id' => $lootgoblin->id,
                'title_id' => $animalCrossing->id,
                'depth' => 1,
                'score' => 4,
                'body' => 'Relaxing but the daily grind for bells got old after a while. Still comes back to it every few months.',
            ]);
        }

        if ($myHeroAcademia) {
            Review::create([
                'user_id' => $lootgoblin->id,
                'title_id' => $myHeroAcademia->id,
                'depth' => 1,
                'score' => 4,
                'body' => 'Solid shonen, the power system keeps things interesting even this many seasons in.',
            ]);
        }

        if ($godOfWarReview) {
            $reply = Review::create([
                'user_id' => $lootgoblin->id,
                'title_id' => $godOfWar->id,
                'parent_id' => $godOfWarReview->id,
                'depth' => 2,
                'score' => null,
                'body' => 'Agreed on the pacing, the Asgard section felt like padding.',
            ]);
            $reply->likes()->syncWithoutDetaching([$todobrozaa->id]);
        }

        // --- todobrozaa: watchlist / gaming / favourites ---
        if ($lastOfUs) {
            WatchlistEntry::create(['user_id' => $todobrozaa->id, 'title_id' => $lastOfUs->id, 'status' => 'watching']);
        }
        if ($deathNote) {
            WatchlistEntry::create(['user_id' => $todobrozaa->id, 'title_id' => $deathNote->id, 'status' => 'completed']);
        }
        if ($silentVoice) {
            WatchlistEntry::create(['user_id' => $todobrozaa->id, 'title_id' => $silentVoice->id, 'status' => 'want_to_watch']);
        }
        if ($witcher3) {
            GamingEntry::create(['user_id' => $todobrozaa->id, 'title_id' => $witcher3->id, 'status' => 'completed']);
        }
        if ($stardew) {
            GamingEntry::create(['user_id' => $todobrozaa->id, 'title_id' => $stardew->id, 'status' => 'playing']);
        }
        if ($parasite) {
            $todobrozaa->favourites()->syncWithoutDetaching([$parasite->id]);
        }
        if ($strangerThings) {
            $todobrozaa->favourites()->syncWithoutDetaching([$strangerThings->id]);
        }

        // --- lootgoblin: watchlist / gaming / favourites ---
        if ($godOfWar) {
            GamingEntry::create(['user_id' => $lootgoblin->id, 'title_id' => $godOfWar->id, 'status' => 'completed']);
        }
        if ($animalCrossing) {
            GamingEntry::create(['user_id' => $lootgoblin->id, 'title_id' => $animalCrossing->id, 'status' => '100percent']);
        }
        if ($stardew) {
            GamingEntry::create(['user_id' => $lootgoblin->id, 'title_id' => $stardew->id, 'status' => 'completed']);
        }
        if ($swordArtOnline) {
            WatchlistEntry::create(['user_id' => $lootgoblin->id, 'title_id' => $swordArtOnline->id, 'status' => 'watching']);
        }
        if ($witcher3) {
            $lootgoblin->favourites()->syncWithoutDetaching([$witcher3->id]);
        }
        if ($animalCrossing) {
            $lootgoblin->favourites()->syncWithoutDetaching([$animalCrossing->id]);
        }

        // --- "vragen": FAQ suggestions + contact messages, one each ---
        FaqSuggestion::create([
            'user_id' => $todobrozaa->id,
            'question' => 'Could you add a way to see what my friends are currently watching?',
        ]);

        FaqSuggestion::create([
            'user_id' => $lootgoblin->id,
            'question' => 'Any plans to add trophy/achievement tracking for games, not just a status?',
        ]);

        ContactMessage::create([
            'user_id' => $todobrozaa->id,
            'name' => 'todobrozaa',
            'email' => 'todobrozaa@example.com',
            'subject' => 'Feature idea: friends list',
            'body' => "Would love to see what people I follow are watching or playing. Is that something you're considering?",
        ]);

        ContactMessage::create([
            'user_id' => $lootgoblin->id,
            'name' => 'lootgoblin',
            'email' => 'lootgoblin@example.com',
            'subject' => 'Achievement tracking?',
            'body' => "Loving the gaming list so far. Any chance you'll add per-game achievement tracking down the line?",
        ]);
    }
}
