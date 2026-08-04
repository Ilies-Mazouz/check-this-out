<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TitleImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TitleImportController extends Controller
{
    /**
     * Items imported per /step call. Kept small on purpose — each request
     * has to stay fast, both so the progress bar feels responsive and so a
     * long-running import never blocks other visitors on a single-threaded
     * dev server.
     */
    private const BATCH_SIZE = 2;

    private const CACHE_TTL_MINUTES = 30;

    public function start(Request $request, TitleImportService $service): JsonResponse
    {
        $validated = $request->validate([
            'types' => ['required', 'array', 'min:1'],
            'types.*' => ['in:movie,series,anime,game'],
            'genre' => ['nullable', 'string', 'max:50'],
            'mode' => ['required', 'in:trending,newest'],
            'count' => ['required', 'integer', 'min:1', 'max:40'],
        ]);

        $plan = $service->plan(
            $validated['types'],
            $validated['genre'] ?? null,
            $validated['mode'],
            $validated['count']
        );

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

    public function step(Request $request, string $session, TitleImportService $service): JsonResponse
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
            $title = $service->importOne($candidate);

            $imported[] = [
                'title' => $candidate['title'],
                'type' => $candidate['type'],
                'success' => $title !== null,
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
        return "title_import:{$session}";
    }
}
