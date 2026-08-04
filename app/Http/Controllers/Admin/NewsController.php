<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use App\Support\ImageResizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $articles = NewsArticle::query()->orderByDesc('created_at')->paginate(15);

        return view('admin.news.index', ['articles' => $articles]);
    }

    public function create(): View
    {
        return view('admin.news.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $article = new NewsArticle($validated);
        $article->user_id = $request->user()->id;
        $article->slug = $this->uniqueSlug($validated['title']);

        if ($request->hasFile('cover_image')) {
            $article->cover_image = ImageResizer::storeUploaded($request->file('cover_image'), 'news');
        }

        $article->save();

        return redirect()->route('admin.news.index')->with('status', 'news-created');
    }

    public function edit(NewsArticle $news): View
    {
        return view('admin.news.edit', ['article' => $news]);
    }

    public function update(Request $request, NewsArticle $news): RedirectResponse
    {
        $validated = $this->validated($request, $news->id);

        if ($news->title !== $validated['title']) {
            $news->slug = $this->uniqueSlug($validated['title'], $news->id);
        }

        $news->fill($validated);

        if ($request->hasFile('cover_image')) {
            if ($news->cover_image) {
                Storage::disk('public')->delete($news->cover_image);
            }

            $news->cover_image = ImageResizer::storeUploaded($request->file('cover_image'), 'news');
        }

        $news->save();

        return redirect()->route('admin.news.index')->with('status', 'news-updated');
    }

    public function destroy(NewsArticle $news): RedirectResponse
    {
        if ($news->cover_image) {
            Storage::disk('public')->delete($news->cover_image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')->with('status', 'news-deleted');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 1;

        while (NewsArticle::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
