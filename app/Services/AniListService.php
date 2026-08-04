<?php

namespace App\Services;

use App\Support\ImageResizer;
use Illuminate\Support\Facades\Http;

class AniListService
{
    private const BASE_URL = 'https://graphql.anilist.co';

    private const SEARCH_QUERY = <<<'GRAPHQL'
        query ($search: String, $perPage: Int) {
            Page(page: 1, perPage: $perPage) {
                media(search: $search, type: ANIME) {
                    id
                    title { romaji english }
                    startDate { year month day }
                    description(asHtml: false)
                    coverImage { large }
                }
            }
        }
        GRAPHQL;

    private const DETAIL_QUERY = <<<'GRAPHQL'
        query ($id: Int) {
            Media(id: $id, type: ANIME) {
                id
                title { romaji english }
                startDate { year month day }
                description(asHtml: false)
                coverImage { large }
                genres
            }
        }
        GRAPHQL;

    private const DISCOVER_QUERY = <<<'GRAPHQL'
        query ($genre: String, $sort: [MediaSort], $perPage: Int) {
            Page(page: 1, perPage: $perPage) {
                media(genre: $genre, sort: $sort, type: ANIME, status_not: NOT_YET_RELEASED, startDate_greater: 19700101) {
                    id
                    title { romaji english }
                    startDate { year month day }
                    description(asHtml: false)
                    coverImage { large }
                }
            }
        }
        GRAPHQL;

    /**
     * AniList's genre enum doesn't include demographic labels like Shounen
     * or Isekai (those are tags, not genres) — only real genres can be
     * filtered on here. Anything else falls back to unfiltered results.
     */
    private const SUPPORTED_GENRES = [
        'Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy', 'Horror',
        'Mystery', 'Romance', 'Sci-Fi', 'Slice of Life', 'Thriller', 'Mecha',
    ];

    /**
     * List anime for the catalogue importer — trending (popularity-sorted)
     * or newest (start-date-sorted), optionally filtered by genre.
     */
    public function discover(string $mode, ?string $genreName = null): array
    {
        $genre = ($genreName && in_array($genreName, self::SUPPORTED_GENRES, true)) ? $genreName : null;

        $response = Http::retry(2, 300)
            ->post(self::BASE_URL, [
                'query' => self::DISCOVER_QUERY,
                'variables' => [
                    'genre' => $genre,
                    'sort' => [$mode === 'newest' ? 'START_DATE_DESC' : 'POPULARITY_DESC'],
                    'perPage' => 20,
                ],
            ])
            ->throw();

        return collect($response->json('data.Page.media', []))
            ->map(fn (array $media) => $this->normalizeResult($media))
            ->all();
    }

    /**
     * Search AniList for anime matching the given query.
     * No API key required — AniList's GraphQL API is fully public for reads.
     */
    public function search(string $query): array
    {
        $response = Http::retry(2, 300)
            ->post(self::BASE_URL, [
                'query' => self::SEARCH_QUERY,
                'variables' => ['search' => $query, 'perPage' => 8],
            ])
            ->throw();

        return collect($response->json('data.Page.media', []))
            ->map(fn (array $media) => $this->normalizeResult($media))
            ->all();
    }

    /**
     * Fetch full details for a single anime and download its cover to
     * local storage so it renders the same way as manually uploaded covers.
     */
    public function import(string $id): array
    {
        $response = Http::retry(2, 300)
            ->post(self::BASE_URL, [
                'query' => self::DETAIL_QUERY,
                'variables' => ['id' => (int) $id],
            ])
            ->throw();

        $media = $response->json('data.Media');

        return [
            'title' => $this->title($media['title'] ?? []),
            'synopsis' => $this->cleanDescription($media['description'] ?? null),
            'release_date' => $this->releaseDate($media['startDate'] ?? null),
            'cover_image' => $this->downloadCover($media['coverImage']['large'] ?? null),
            'genres' => $media['genres'] ?? [],
        ];
    }

    private function normalizeResult(array $media): array
    {
        $releaseDate = $this->releaseDate($media['startDate'] ?? null);

        return [
            'id' => $media['id'],
            'title' => $this->title($media['title'] ?? []),
            'release_date' => $releaseDate,
            'year' => $releaseDate ? substr($releaseDate, 0, 4) : null,
            'overview' => $this->cleanDescription($media['description'] ?? null),
            'poster_url' => $media['coverImage']['large'] ?? null,
        ];
    }

    private function title(array $title): string
    {
        return $title['english'] ?: ($title['romaji'] ?? 'Untitled');
    }

    private function releaseDate(?array $date): ?string
    {
        if (! $date || empty($date['year'])) {
            return null;
        }

        $month = str_pad((string) ($date['month'] ?? 1), 2, '0', STR_PAD_LEFT);
        $day = str_pad((string) ($date['day'] ?? 1), 2, '0', STR_PAD_LEFT);

        return "{$date['year']}-{$month}-{$day}";
    }

    private function cleanDescription(?string $description): ?string
    {
        if (! $description) {
            return null;
        }

        $withBreaks = str_replace(['<br>', '<br/>', '<br />'], "\n", $description);

        return trim(strip_tags($withBreaks)) ?: null;
    }

    private function downloadCover(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $image = Http::retry(2, 300)->get($url)->throw()->body();

        return ImageResizer::storeBytes($image, 'titles');
    }
}
