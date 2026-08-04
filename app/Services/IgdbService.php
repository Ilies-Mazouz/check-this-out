<?php

namespace App\Services;

use App\Support\ImageResizer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IgdbService
{
    private const TOKEN_URL = 'https://id.twitch.tv/oauth2/token';

    private const BASE_URL = 'https://api.igdb.com/v4';

    /**
     * Our seeded game genre names mapped to IGDB's own genre names, which
     * don't always match exactly (e.g. "RPG" vs "Role-playing (RPG)").
     * "Horror" has no IGDB genre equivalent (it's more a theme there), so
     * it's deliberately absent — that combination just returns unfiltered.
     */
    private const GENRE_MAP = [
        'RPG' => 'Role-playing (RPG)',
        'FPS' => 'Shooter',
        'Strategy' => 'Strategy',
        'Sports' => 'Sport',
        'Puzzle' => 'Puzzle',
        'Fighting' => 'Fighting',
        'Simulation' => 'Simulator',
    ];

    /**
     * List games for the catalogue importer — trending (rating-count-sorted,
     * as a popularity proxy since IGDB has no single "popularity" field on
     * this endpoint) or newest (release-date-sorted), optionally genre
     * filtered, always excluding unreleased games.
     */
    public function discover(string $mode, ?string $genreName = null): array
    {
        $now = now()->timestamp;
        $genreClause = ($genreName && isset(self::GENRE_MAP[$genreName]))
            ? ' & genres.name = "'.self::GENRE_MAP[$genreName].'"'
            : '';

        if ($mode === 'newest') {
            $where = "first_release_date < {$now} & first_release_date != null{$genreClause}";
            $sort = 'first_release_date desc';
        } else {
            $where = "first_release_date < {$now} & total_rating_count > 10{$genreClause}";
            $sort = 'total_rating_count desc';
        }

        $response = $this->query('/games', <<<APICALYPSE
            fields name,cover.url,first_release_date,summary;
            where {$where};
            sort {$sort};
            limit 20;
            APICALYPSE
        );

        return collect($response)
            ->map(fn (array $game) => $this->normalizeResult($game))
            ->all();
    }

    /**
     * Search IGDB for games matching the given query.
     */
    public function search(string $query): array
    {
        $escaped = str_replace('"', '', $query);

        $response = $this->query('/games', <<<APICALYPSE
            search "{$escaped}";
            fields name,cover.url,first_release_date,summary;
            limit 8;
            APICALYPSE
        );

        return collect($response)
            ->map(fn (array $game) => $this->normalizeResult($game))
            ->all();
    }

    /**
     * Fetch full details for a single game and download its cover to local
     * storage so it renders the same way as manually uploaded covers.
     */
    public function import(string $id): array
    {
        $response = $this->query('/games', <<<APICALYPSE
            fields name,cover.url,first_release_date,summary,genres.name;
            where id = {$id};
            APICALYPSE
        );

        $game = $response[0] ?? null;

        if (! $game) {
            throw new \RuntimeException("IGDB game [{$id}] not found.");
        }

        return [
            'title' => $game['name'],
            'synopsis' => $game['summary'] ?? null,
            'release_date' => isset($game['first_release_date']) ? date('Y-m-d', $game['first_release_date']) : null,
            'cover_image' => $this->downloadCover($game['cover']['url'] ?? null),
            'genres' => collect($game['genres'] ?? [])->pluck('name')->all(),
        ];
    }

    private function normalizeResult(array $game): array
    {
        return [
            'id' => $game['id'],
            'title' => $game['name'],
            'release_date' => isset($game['first_release_date']) ? date('Y-m-d', $game['first_release_date']) : null,
            'year' => isset($game['first_release_date']) ? date('Y', $game['first_release_date']) : null,
            'overview' => $game['summary'] ?? null,
            'poster_url' => isset($game['cover']['url']) ? $this->coverUrl($game['cover']['url']) : null,
        ];
    }

    private function coverUrl(string $url): string
    {
        $url = str_starts_with($url, '//') ? 'https:'.$url : $url;

        return str_replace('t_thumb', 't_cover_big', $url);
    }

    private function downloadCover(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $image = Http::retry(2, 300)->get($this->coverUrl($url))->throw()->body();

        return ImageResizer::storeBytes($image, 'titles');
    }

    /**
     * Run an Apicalypse query against the given IGDB endpoint.
     */
    private function query(string $endpoint, string $body): array
    {
        return Http::retry(2, 300)
            ->withToken($this->accessToken())
            ->withHeaders(['Client-ID' => config('services.igdb.client_id')])
            ->withBody($body, 'text/plain')
            ->post(self::BASE_URL.$endpoint)
            ->throw()
            ->json();
    }

    /**
     * IGDB auth goes through Twitch's app-access-token flow. Tokens are
     * valid for ~60 days, so cache well under that instead of fetching one
     * per request.
     */
    private function accessToken(): string
    {
        return Cache::remember('igdb_access_token', now()->addDays(30), function () {
            $response = Http::asForm()->post(self::TOKEN_URL, [
                'client_id' => config('services.igdb.client_id'),
                'client_secret' => config('services.igdb.client_secret'),
                'grant_type' => 'client_credentials',
            ])->throw();

            return $response->json('access_token');
        });
    }
}
