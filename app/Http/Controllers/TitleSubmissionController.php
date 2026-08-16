<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ImportsExternalTitles;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\Title;
use App\Services\AniListService;
use App\Services\IgdbService;
use App\Services\TmdbService;
use App\Support\ImageResizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class TitleSubmissionController extends Controller
{
    use ImportsExternalTitles;

    public function create(): View
    {
        return view('titles.submit', [
            'genres' => Genre::orderBy('name')->get(),
            'platforms' => Platform::orderBy('name')->get(),
        ]);
    }

    public function search(Request $request, TmdbService $tmdb, AniListService $aniList, IgdbService $igdb): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:movie,series,anime,game'],
            'q' => ['required', 'string', 'min:2', 'max:255'],
        ]);

        try {
            $results = match ($validated['type']) {
                'movie', 'series' => $tmdb->search($validated['q'], $validated['type']),
                'anime' => $aniList->search($validated['q']),
                'game' => $igdb->search($validated['q']),
            };
        } catch (Throwable $e) {
            Log::warning('External title search failed.', [
                'type' => $validated['type'],
                'query' => $validated['q'],
                'error' => $e->getMessage(),
            ]);

            return response()->json(['results' => [], 'unavailable' => true]);
        }

        return response()->json(['results' => $results]);
    }

    public function store(Request $request, TmdbService $tmdb, AniListService $aniList, IgdbService $igdb): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:movie,series,anime,game'],
            'synopsis' => ['nullable', 'string', 'max:3000'],
            'release_date' => ['nullable', 'date'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:genres,id'],
            'platforms' => ['nullable', 'array'],
            'platforms.*' => ['exists:platforms,id'],
            'external_id' => ['nullable', 'string'],
        ]);

        $apiSource = 'manual';
        $apiId = null;
        $sourceSlug = null;
        $titleName = $validated['title'];
        $synopsis = $validated['synopsis'] ?? null;
        $releaseDate = $validated['release_date'] ?? null;
        $importedCoverImage = null;

        if ($imported = $this->importExternalTitle($validated['type'], $validated['external_id'] ?? null, $tmdb, $aniList, $igdb)) {
            $apiSource = $imported['source'];
            $apiId = $validated['external_id'];
            $sourceSlug = $imported['slug'] ?? null;
            $titleName = $imported['title'];
            $synopsis = $imported['synopsis'];
            $releaseDate = $imported['release_date'];
            $importedCoverImage = $imported['cover_image'];
        }

        $title = new Title([
            'api_source' => $apiSource,
            'api_id' => $apiId,
            'source_slug' => $sourceSlug,
            'type' => $validated['type'],
            'title' => $titleName,
            'synopsis' => $synopsis,
            'release_date' => $releaseDate,
            'status' => 'pending',
        ]);
        $title->slug = $this->uniqueSlug($titleName);
        $title->submitted_by = $request->user()->id;

        if ($request->hasFile('cover_image')) {
            $title->cover_image = ImageResizer::storeUploaded($request->file('cover_image'), 'titles');
        } elseif ($importedCoverImage) {
            $title->cover_image = $importedCoverImage;
        }

        $title->save();
        $title->genres()->sync($validated['genres'] ?? []);
        $title->platforms()->sync($validated['platforms'] ?? []);

        return redirect()->route('catalogue')->with('status', 'title-submitted');
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
