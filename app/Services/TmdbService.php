<?php

namespace App\Services;

use App\Support\ImageResizer;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TmdbService
{
    private const BASE_URL = 'https://api.themoviedb.org/3';

    private const IMAGE_BASE = 'https://image.tmdb.org/t/p/w500';

    /**
     * TMDB uses different genre ids for movies vs TV, and TV merges some
     * pairs together (e.g. Action & Adventure is one genre, not two).
     */
    private const MOVIE_GENRE_IDS = [
        'Action' => 28, 'Adventure' => 12, 'Animation' => 16, 'Comedy' => 35,
        'Documentary' => 99, 'Drama' => 18, 'Fantasy' => 14, 'Horror' => 27,
        'Mystery' => 9648, 'Romance' => 10749, 'Sci-Fi' => 878, 'Thriller' => 53,
    ];

    private const TV_GENRE_IDS = [
        'Action' => 10759, 'Adventure' => 10759, 'Comedy' => 35, 'Documentary' => 99,
        'Drama' => 18, 'Fantasy' => 10765, 'Mystery' => 9648, 'Reality' => 10764,
        'Romance' => 10749, 'Sci-Fi' => 10765, 'Talk Show' => 10767,
    ];

    /**
     * List movies/series for the catalogue importer — either trending
     * (popularity-sorted) or newest (release-date-sorted, excluding
     * unreleased titles and near-zero-vote noise). Returns null for the
     * genre filter silently if this source has no mapping for it, rather
     * than erroring — the caller just gets unfiltered results.
     */
    public function discover(string $type, string $mode, ?string $genreName = null): array
    {
        $endpoint = $this->endpointFor($type);
        $genreMap = $type === 'movie' ? self::MOVIE_GENRE_IDS : self::TV_GENRE_IDS;
        $dateField = $type === 'movie' ? 'primary_release_date' : 'first_air_date';

        $params = [
            'api_key' => config('services.tmdb.key'),
            'sort_by' => $mode === 'newest' ? "{$dateField}.desc" : 'popularity.desc',
            'vote_count.gte' => $mode === 'newest' ? 5 : 20,
            'include_adult' => false,
        ];

        if ($genreName && isset($genreMap[$genreName])) {
            $params['with_genres'] = $genreMap[$genreName];
        }

        if ($mode === 'newest') {
            $params["{$dateField}.lte"] = now()->format('Y-m-d');
        }

        $response = Http::baseUrl(self::BASE_URL)
            ->retry(2, 300)
            ->get("/discover/{$endpoint}", $params)
            ->throw();

        return collect($response->json('results', []))
            ->map(fn (array $result) => $this->normalizeSearchResult($result, $type))
            ->all();
    }

    /**
     * Search TMDB for movies or series matching the given query.
     */
    public function search(string $query, string $type): array
    {
        $response = Http::baseUrl(self::BASE_URL)
            ->retry(2, 300)
            ->get('/search/'.$this->endpointFor($type), [
                'api_key' => config('services.tmdb.key'),
                'query' => $query,
                'include_adult' => false,
            ])
            ->throw();

        return collect($response->json('results', []))
            ->take(8)
            ->map(fn (array $result) => $this->normalizeSearchResult($result, $type))
            ->all();
    }

    /**
     * Fetch full details for a single TMDB result and download its poster
     * to local storage so it renders the same way as manually uploaded covers.
     */
    public function import(string $id, string $type): array
    {
        $response = Http::baseUrl(self::BASE_URL)
            ->retry(2, 300)
            ->get('/'.$this->endpointFor($type).'/'.$id, [
                'api_key' => config('services.tmdb.key'),
            ])
            ->throw();

        $details = $response->json();

        return [
            'title' => $type === 'movie' ? $details['title'] : $details['name'],
            'synopsis' => $details['overview'] ?: null,
            'release_date' => ($type === 'movie' ? $details['release_date'] : $details['first_air_date']) ?: null,
            'cover_image' => $this->downloadPoster($details['poster_path'] ?? null),
            'genres' => $this->translateGenres($details['genres'] ?? []),
        ];
    }

    /**
     * TMDB sometimes merges genres we keep separate ("Action & Adventure",
     * "Sci-Fi & Fantasy") or names them differently ("Science Fiction").
     * Anything not recognized here is simply dropped, not guessed at.
     */
    private function translateGenres(array $genres): array
    {
        $translations = [
            'Science Fiction' => ['Sci-Fi'],
            'Action & Adventure' => ['Action', 'Adventure'],
            'Sci-Fi & Fantasy' => ['Sci-Fi', 'Fantasy'],
            'War & Politics' => [],
            'Talk' => ['Talk Show'],
            'Soap' => [],
            'Kids' => [],
            'News' => [],
            'Crime' => [],
            'Family' => [],
            'History' => [],
            'Music' => [],
            'TV Movie' => [],
            'War' => [],
            'Western' => [],
        ];

        return collect($genres)
            ->pluck('name')
            ->flatMap(fn (string $name) => $translations[$name] ?? [$name])
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeSearchResult(array $result, string $type): array
    {
        $releaseDate = $type === 'movie' ? ($result['release_date'] ?? null) : ($result['first_air_date'] ?? null);

        return [
            'id' => $result['id'],
            'title' => $type === 'movie' ? $result['title'] : $result['name'],
            'release_date' => $releaseDate ?: null,
            'year' => $releaseDate ? substr($releaseDate, 0, 4) : null,
            'overview' => $result['overview'] ?? null,
            'poster_url' => $result['poster_path'] ? self::IMAGE_BASE.$result['poster_path'] : null,
        ];
    }

    private function downloadPoster(?string $posterPath): ?string
    {
        if (! $posterPath) {
            return null;
        }

        $image = Http::retry(2, 300)->get(self::IMAGE_BASE.$posterPath)->throw()->body();

        return ImageResizer::storeBytes($image, 'titles');
    }

    private function endpointFor(string $type): string
    {
        return match ($type) {
            'movie' => 'movie',
            'series' => 'tv',
            default => throw new RuntimeException("TMDB does not support type [{$type}]."),
        };
    }
}
