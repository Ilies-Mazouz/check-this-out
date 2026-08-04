<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RssNewsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class NewsImportController extends Controller
{
    /**
     * Items imported per /step call — same reasoning as the catalogue
     * importer: keep each request short so the progress bar stays
     * responsive and nothing blocks other visitors on a single-threaded
     * dev server.
     */
    private const BATCH_SIZE = 3;

    private const CACHE_TTL_MINUTES = 30;

    public function start(RssNewsService $service): JsonResponse
    {
        $plan = $service->plan();
        $session = (string) Str::uuid();

        Cache::put($this->cacheKey($session), [
            'plan' => $plan,
            'index' => 0,
            'cancelled' => false,
        ], now()->addMinutes(self::CACHE_TTL_MINUTES));

        return response()->json([
            'session' => $session,
            'total' => count($plan),
        ]);
    }

    public function step(string $session, RssNewsService $service): JsonResponse
    {
        $key = $this->cacheKey($session);
        $state = Cache::get($key);

        if (! $state || $state['cancelled'] || $state['index'] >= count($state['plan'])) {
            Cache::forget($key);

            return response()->json([
                'done' => true,
                'progress' => $state['index'] ?? 0,
                'total' => count($state['plan'] ?? []),
                'imported' => [],
            ]);
        }

        $batch = array_slice($state['plan'], $state['index'], self::BATCH_SIZE);
        $imported = [];

        foreach ($batch as $candidate) {
            $imported[] = [
                'title' => $candidate['title'],
                'success' => $service->importOne($candidate),
            ];
        }

        $state['index'] += count($batch);
        $done = $state['index'] >= count($state['plan']);

        if ($done) {
            Cache::forget($key);
        } else {
            Cache::put($key, $state, now()->addMinutes(self::CACHE_TTL_MINUTES));
        }

        return response()->json([
            'done' => $done,
            'progress' => $state['index'],
            'total' => count($state['plan']),
            'imported' => $imported,
        ]);
    }

    public function cancel(string $session): JsonResponse
    {
        $key = $this->cacheKey($session);
        $state = Cache::get($key);

        if ($state) {
            $state['cancelled'] = true;
            Cache::put($key, $state, now()->addMinutes(self::CACHE_TTL_MINUTES));
        }

        return response()->json(['cancelled' => true]);
    }

    private function cacheKey(string $session): string
    {
        return "news_import:{$session}";
    }
}
