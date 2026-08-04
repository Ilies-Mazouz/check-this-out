<?php

namespace App\Services;

use App\Models\NewsArticle;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;

class RssNewsService
{
    /**
     * Real, free, public RSS feeds — no API key or account needed for any of them.
     */
    private const FEEDS = [
        'https://feeds.ign.com/ign/games-all',
        'https://www.animenewsnetwork.com/all/rss.xml',
        'https://variety.com/v/film/feed/',
        'https://variety.com/v/tv/feed/',
    ];

    private const ITEMS_PER_FEED = 12;

    /**
     * Pull the latest items from each configured feed and store any that
     * aren't already in the database (matched by source URL). Returns the
     * number of new articles created. Used by the CLI command / schedule,
     * where there's no UI to show progress to.
     */
    public function import(): int
    {
        $created = 0;

        foreach ($this->plan() as $candidate) {
            if ($this->importOne($candidate)) {
                $created++;
            }
        }

        return $created;
    }

    /**
     * Fetch every feed and return the items that aren't already in the
     * database yet, ready to be created one at a time by importOne(). Used
     * by the admin panel's chunked/cancellable import UI.
     */
    public function plan(): array
    {
        $candidates = [];

        foreach (self::FEEDS as $feedUrl) {
            foreach ($this->fetchItems($feedUrl) as $item) {
                if (NewsArticle::where('source_url', $item['link'])->exists()) {
                    continue;
                }

                $candidates[] = $item;
            }
        }

        return $candidates;
    }

    /**
     * Create a single planned candidate. Returns false (and skips) if it
     * turns out to already exist — guards against the same item slipping in
     * twice between planning and execution.
     */
    public function importOne(array $candidate): bool
    {
        $admin = User::where('is_admin', true)->first();

        if (! $admin || NewsArticle::where('source_url', $candidate['link'])->exists()) {
            return false;
        }

        NewsArticle::create([
            'user_id' => $admin->id,
            'title' => $candidate['title'],
            'slug' => $this->uniqueSlug($candidate['title']),
            'body' => $candidate['body'],
            'source_url' => $candidate['link'],
            'published_at' => $candidate['published_at'],
        ]);

        return true;
    }

    private function fetchItems(string $feedUrl): array
    {
        try {
            $response = Http::retry(2, 300)
                ->withUserAgent('Mozilla/5.0 (compatible; CheckThisOutBot/1.0)')
                ->get($feedUrl)
                ->throw();

            $xml = new SimpleXMLElement($response->body());
        } catch (Throwable $e) {
            Log::warning('RSS feed import failed.', ['feed' => $feedUrl, 'error' => $e->getMessage()]);

            return [];
        }

        $items = [];

        foreach ($xml->channel->item as $item) {
            if (count($items) >= self::ITEMS_PER_FEED) {
                break;
            }

            $description = trim(strip_tags((string) $item->description));

            $items[] = [
                'title' => trim((string) $item->title),
                'link' => trim((string) $item->link),
                'body' => Str::limit($description, 1000, ''),
                'published_at' => $this->parseDate((string) $item->pubDate),
            ];
        }

        return $items;
    }

    private function parseDate(string $pubDate): string
    {
        $timestamp = strtotime($pubDate);

        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : now();
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 1;

        while (NewsArticle::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
