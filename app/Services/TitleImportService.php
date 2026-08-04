<?php

namespace App\Services;

use App\Models\Genre;
use App\Models\Title;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class TitleImportService
{
    private const TYPES = ['movie', 'series', 'anime', 'game'];

    public function __construct(
        private TmdbService $tmdb,
        private AniListService $aniList,
        private IgdbService $igdb,
    ) {
    }

    /**
     * Build an ordered list of import candidates, balanced round-robin
     * across the requested types, with a second pass that redistributes
     * any shortfall (e.g. no new anime available) to types that had extra
     * candidates on offer. Deduped against the existing catalogue by both
     * source id and normalized title, so it never re-imports the same
     * thing twice or under a slightly different source.
     */
    public function plan(array $types, ?string $genre, string $mode, int $count): array
    {
        $types = array_values(array_intersect($types, self::TYPES));

        if (empty($types) || $count < 1) {
            return [];
        }

        $existing = Title::query()->get(['api_source', 'api_id', 'title']);
        $existingIds = $existing->map(fn ($t) => "{$t->api_source}:{$t->api_id}")->flip();
        $existingTitles = $existing->map(fn ($t) => Str::lower($t->title))->flip();

        $pools = [];
        foreach ($types as $type) {
            $pools[$type] = $this->fetchCandidates($type, $mode, $genre, $existingIds, $existingTitles);
        }

        $typeCount = count($types);
        $base = intdiv($count, $typeCount);
        $remainder = $count % $typeCount;

        $targets = [];
        foreach (array_values($types) as $i => $type) {
            $targets[$type] = $base + ($i < $remainder ? 1 : 0);
        }

        $plan = [];
        $shortfall = 0;

        foreach ($types as $type) {
            $take = array_splice($pools[$type], 0, $targets[$type]);
            $plan = array_merge($plan, $take);

            if (count($take) < $targets[$type]) {
                $shortfall += $targets[$type] - count($take);
            }
        }

        // Second pass: hand the shortfall to whichever types still have
        // candidates left over, one at a time, round-robin.
        while ($shortfall > 0) {
            $tookAny = false;

            foreach ($types as $type) {
                if ($shortfall <= 0) {
                    break;
                }

                if (! empty($pools[$type])) {
                    $plan[] = array_shift($pools[$type]);
                    $shortfall--;
                    $tookAny = true;
                }
            }

            if (! $tookAny) {
                break;
            }
        }

        return $plan;
    }

    /**
     * Import a single planned candidate. Returns the created title, or null
     * if the fetch failed or it turned out to already exist (logged either
     * way) — one bad item never stops the rest of the batch.
     */
    public function importOne(array $candidate): ?Title
    {
        if (Title::where('api_source', $candidate['source'])->where('api_id', $candidate['external_id'])->exists()) {
            return null;
        }

        try {
            $imported = match ($candidate['source']) {
                'tmdb' => $this->tmdb->import($candidate['external_id'], $candidate['type']),
                'anilist' => $this->aniList->import($candidate['external_id']),
                'igdb' => $this->igdb->import($candidate['external_id']),
            };
        } catch (Throwable $e) {
            Log::warning('Catalogue import of a single title failed.', ['candidate' => $candidate, 'error' => $e->getMessage()]);

            return null;
        }

        $title = Title::create([
            'api_source' => $candidate['source'],
            'api_id' => $candidate['external_id'],
            'type' => $candidate['type'],
            'title' => $imported['title'],
            'slug' => $this->uniqueSlug($imported['title']),
            'synopsis' => $imported['synopsis'],
            'cover_image' => $imported['cover_image'],
            'release_date' => $imported['release_date'],
            'status' => 'accepted',
        ]);

        // Genre names aren't unique in this table (e.g. "Horror" exists for
        // both 'all' and 'game'), so dedupe by name before syncing —
        // otherwise the same genre shows up twice on the title.
        $genreIds = Genre::whereIn('name', $imported['genres'] ?? [])->get()->unique('name')->pluck('id');
        $title->genres()->sync($genreIds);

        return $title;
    }

    private function fetchCandidates(string $type, string $mode, ?string $genre, $existingIds, $existingTitles): array
    {
        $source = match ($type) {
            'movie', 'series' => 'tmdb',
            'anime' => 'anilist',
            'game' => 'igdb',
        };

        try {
            $results = match ($type) {
                'movie', 'series' => $this->tmdb->discover($type, $mode, $genre),
                'anime' => $this->aniList->discover($mode, $genre),
                'game' => $this->igdb->discover($mode, $genre),
            };
        } catch (Throwable $e) {
            Log::warning('Catalogue import discover failed.', ['type' => $type, 'error' => $e->getMessage()]);

            return [];
        }

        return collect($results)
            ->filter(fn (array $r) => ! empty($r['release_date']) && ! empty($r['overview']))
            ->reject(fn (array $r) => isset($existingIds["{$source}:{$r['id']}"]))
            ->reject(fn (array $r) => isset($existingTitles[Str::lower($r['title'])]))
            ->unique(fn (array $r) => $r['id'])
            ->map(fn (array $r) => [
                'type' => $type,
                'source' => $source,
                'external_id' => (string) $r['id'],
                'title' => $r['title'],
            ])
            ->values()
            ->all();
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 1;

        while (Title::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
