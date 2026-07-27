<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Platform;
use App\Models\Title;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TitleSubmissionController extends Controller
{
    public function create(): View
    {
        return view('titles.submit', [
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
            'status' => 'pending',
        ]);
        $title->slug = $this->uniqueSlug($validated['title']);
        $title->submitted_by = $request->user()->id;

        if ($request->hasFile('cover_image')) {
            $title->cover_image = $request->file('cover_image')->store('titles', 'public');
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
