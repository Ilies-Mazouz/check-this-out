<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Models\NewsComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsCommentController extends Controller
{
    public function store(Request $request, NewsArticle $news): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $news->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return back()->with('status', 'comment-posted');
    }

    public function destroy(Request $request, NewsArticle $news, NewsComment $comment): RedirectResponse
    {
        abort_unless($comment->news_article_id === $news->id, 404);
        abort_unless($comment->user_id === $request->user()->id || $request->user()->is_admin, 403);

        $comment->delete();

        return back()->with('status', 'comment-deleted');
    }
}
