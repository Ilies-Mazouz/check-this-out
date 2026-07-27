<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Notification;
use App\Models\Platform;
use App\Models\Title;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TitleController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status', 'pending')->toString();

        $titles = Title::query()
            ->with('submittedBy')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.titles.index', ['titles' => $titles, 'status' => $status]);
    }

    public function create(): View
    {
        return view('admin.titles.create', [
            'genres' => Genre::orderBy('name')->get(),
            'platforms' => Platform::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
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
        ]);

        $title = new Title([
            'api_source' => 'manual',
            'type' => $validated['type'],
            'title' => $validated['title'],
            'synopsis' => $validated['synopsis'] ?? null,
            'release_date' => $validated['release_date'] ?? null,
            'status' => 'accepted',
        ]);
        $title->slug = $this->uniqueSlug($validated['title']);

        if ($request->hasFile('cover_image')) {
            $title->cover_image = $request->file('cover_image')->store('titles', 'public');
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
