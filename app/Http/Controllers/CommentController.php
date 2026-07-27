<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Notification;
use App\Models\Title;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Title $title): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        $depth = 1;
        $parent = null;

        if (! empty($validated['parent_id'])) {
            $parent = Comment::findOrFail($validated['parent_id']);
            abort_unless($parent->title_id === $title->id, 404);
            $depth = min($parent->depth + 1, 3);
        }

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'title_id' => $title->id,
            'parent_id' => $parent?->id,
            'depth' => $depth,
            'body' => $validated['body'],
        ]);

        if ($parent && $parent->user_id !== $request->user()->id) {
            $notifyPref = $parent->user->notificationSetting;

            if (! $notifyPref || $notifyPref->notify_on_comment_reply) {
                Notification::create([
                    'user_id' => $parent->user_id,
                    'type' => 'comment_reply',
                    'data' => [
                        'title_id' => $title->id,
                        'title_slug' => $title->slug,
                        'title_name' => $title->title,
                        'comment_id' => $comment->id,
                        'from_username' => $request->user()->username,
                    ],
                ]);
            }
        }

        return back()->with('status', 'comment-posted');
    }

    public function destroy(Request $request, Title $title, Comment $comment): RedirectResponse
    {
        abort_unless($comment->title_id === $title->id, 404);
        abort_unless($comment->user_id === $request->user()->id || $request->user()->is_admin, 403);

        $comment->delete();

        return back()->with('status', 'comment-deleted');
    }

    public function like(Request $request, Title $title, Comment $comment): RedirectResponse
    {
        abort_unless($comment->title_id === $title->id, 404);

        $comment->likes()->syncWithoutDetaching([$request->user()->id]);

        return back();
    }

    public function unlike(Request $request, Title $title, Comment $comment): RedirectResponse
    {
        abort_unless($comment->title_id === $title->id, 404);

        $comment->likes()->detach($request->user()->id);

        return back();
    }
}
