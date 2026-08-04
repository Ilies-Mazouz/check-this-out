<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ImportsExternalTitles;
use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Notification;
use App\Models\Platform;
use App\Models\Title;
use App\Services\AniListService;
use App\Services\IgdbService;
use App\Services\TmdbService;
use App\Support\ImageResizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TitleController extends Controller
{
    use ImportsExternalTitles;

    public function index(Request $request): View
    {
        $status = $request->string('status', 'pending')->toString();

        $titles = Title::query()
            ->with('submittedBy')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.titles.index', [
            'titles' => $titles,
            'status' => $status,
            'importGenres' => Genre::orderBy('name')->pluck('name')->unique()->values(),
        ]);
    }

    public function create(): View
    {
        return view('admin.titles.create', [
            'genres' => Genre::orderBy('name')->get(),
            'platforms' => Platform::orderBy('name')->get(),
        ]);
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
        $titleName = $validated['title'];
        $synopsis = $validated['synopsis'] ?? null;
        $releaseDate = $validated['release_date'] ?? null;
        $importedCoverImage = null;

        if ($imported = $this->importExternalTitle($validated['type'], $validated['external_id'] ?? null, $tmdb, $aniList, $igdb)) {
            $apiSource = $imported['source'];
            $apiId = $validated['external_id'];
            $titleName = $imported['title'];
            $synopsis = $imported['synopsis'];
            $releaseDate = $imported['release_date'];
            $importedCoverImage = $imported['cover_image'];
        }

        $title = new Title([
            'api_source' => $apiSource,
            'api_id' => $apiId,
            'type' => $validated['type'],
            'title' => $titleName,
            'synopsis' => $synopsis,
            'release_date' => $releaseDate,
            'status' => 'accepted',
        ]);
        $title->slug = $this->uniqueSlug($titleName);

        if ($request->hasFile('cover_image')) {
            $title->cover_image = ImageResizer::storeUploaded($request->file('cover_image'), 'titles');
        } elseif ($importedCoverImage) {
            $title->cover_image = $importedCoverImage;
        }

        $title->save();
        $title->genres()->sync($validated['genres'] ?? []);
        $title->platforms()->sync($validated['platforms'] ?? []);

        return redirect()->route('admin.titles.index', ['status' => 'accepted'])->with('status', 'title-created');
    }

    public function approve(Title $title): RedirectResponse
    {
        $title->update(['status' => 'accepted', 'rejection_reason' => null]);

        $this->notifySubmitter($title, 'submission_accepted');

        return back()->with('status', 'title-approved');
    }

    public function reject(Request $request, Title $title): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $title->update(['status' => 'rejected', 'rejection_reason' => $validated['rejection_reason']]);

        $this->notifySubmitter($title, 'submission_rejected');

        return back()->with('status', 'title-rejected');
    }

    public function destroy(Title $title): RedirectResponse
    {
        $title->delete();

        return back()->with('status', 'title-deleted');
    }

    private function notifySubmitter(Title $title, string $type): void
    {
        if (! $title->submitted_by) {
            return;
        }

        Notification::create([
            'user_id' => $title->submitted_by,
            'type' => $type,
            'data' => [
                'title_id' => $title->id,
                'title_slug' => $title->slug,
                'title_name' => $title->title,
                'rejection_reason' => $title->rejection_reason,
            ],
        ]);
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
