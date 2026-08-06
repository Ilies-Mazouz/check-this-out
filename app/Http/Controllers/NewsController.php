<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $articles = NewsArticle::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('news.index', ['articles' => $articles]);
    }

    public function show(NewsArticle $news): View
    {
        abort_if(
            $news->published_at === null || $news->published_at->isFuture(),
            404
        );

        $blockedIds = auth()->check() ? auth()->user()->blockedUsers()->pluck('users.id') : collect();

        $comments = $news->comments()
            ->with('user')
            ->whereNotIn('user_id', $blockedIds)
            ->latest()
            ->get();

        return view('news.show', ['article' => $news, 'comments' => $comments]);
    }
}
