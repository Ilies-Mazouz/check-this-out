<?php

namespace App\Http\Controllers\Concerns;

use App\Services\AniListService;
use App\Services\IgdbService;
use App\Services\TmdbService;
use Illuminate\Support\Facades\Log;
use Throwable;

trait ImportsExternalTitles
{
    /**
     * Re-fetch authoritative title data from the source API when an external
     * result was selected, rather than trusting client-submitted text fields.
     * Falls back to null (manual entry) if the source API is unavailable, so a
     * flaky third-party API never breaks the submission itself.
     */
    private function importExternalTitle(string $type, ?string $externalId, TmdbService $tmdb, AniListService $aniList, IgdbService $igdb): ?array
    {
        if (empty($externalId)) {
            return null;
        }

        try {
            return match ($type) {
                'movie', 'series' => ['source' => 'tmdb', ...$tmdb->import($externalId, $type)],
                'anime' => ['source' => 'anilist', ...$aniList->import($externalId)],
                'game' => ['source' => 'igdb', ...$igdb->import($externalId)],
                default => null,
            };
        } catch (Throwable $e) {
            Log::warning('External title import failed, falling back to manual entry.', [
                'type' => $type,
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
