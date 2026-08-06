<?php

namespace App\Services;

use App\Models\NewsArticle;
use App\Models\User;
use App\Support\ImageResizer;
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
            'cover_image' => $this->downloadImage($candidate['image_url'] ?? null),
            'source_url' => $candidate['link'],
            'published_at' => $candidate['published_at'],
        ]);

        return true;
    }

    /**
     * Download the article's own featured image (from the feed's
     * media:thumbnail/media:content) and store it locally, resized like
     * every other image on the site. Not every feed provides one (Anime
     * News Network's RSS doesn't) — in that case the article simply has no
     * cover image and falls back to the themed placeholder, rather than
     * showing a photo that doesn't actually belong to the article.
     */
    private function downloadImage(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        try {
            $bytes = Http::timeout(10)
                ->withUserAgent('Mozilla/5.0 (compatible; CheckThisOutBot/1.0)')
                ->get($url)
                ->throw()
                ->body();

            return ImageResizer::storeBytes($bytes, 'news');
        } catch (Throwable $e) {
            Log::warning('RSS article image download failed.', ['url' => $url, 'error' => $e->getMessage()]);

            return null;
        }
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
                'image_url' => $this->extractImageUrl($item),
                'published_at' => $this->parseDate((string) $item->pubDate),
            ];
        }

        return $items;
    }

    /**
     * Pull the article's own featured image out of the feed item, via the
     * Media RSS namespace (media:thumbnail, falling back to media:content).
     * IGN and Variety both provide this; Anime News Network's feed doesn't
     * include any image at all, so this returns null for those items.
     */
    private function extractImageUrl(SimpleXMLElement $item): ?string
    {
        $media = $item->children('http://search.yahoo.com/mrss/');

        if (isset($media->thumbnail)) {
            $url = (string) $media->thumbnail->attributes()->url;

            if ($url !== '') {
                return $url;
            }
        }

        if (isset($media->content)) {
            $url = (string) $media->content->attributes()->url;

            if ($url !== '') {
                return $url;
            }
        }

        return null;
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
