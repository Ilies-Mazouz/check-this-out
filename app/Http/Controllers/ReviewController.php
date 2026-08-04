<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Review;
use App\Models\Title;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Title $title): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
            'parent_id' => ['nullable', 'exists:reviews,id'],
            'score' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $parent = null;

        if (! empty($validated['parent_id'])) {
            $parent = Review::findOrFail($validated['parent_id']);
            abort_unless($parent->title_id === $title->id, 404);
        }

        if ($parent) {
            $this->storeReply($request, $title, $parent, $validated['body']);
        } else {
            $this->storeOrUpdateRootReview($request, $title, $validated);
        }

        return back()->with('status', 'review-saved');
    }

    public function destroy(Request $request, Title $title, Review $review): RedirectResponse
    {
        abort_unless($review->title_id === $title->id, 404);
        abort_unless($review->user_id === $request->user()->id || $request->user()->is_admin, 403);

        $review->delete();

        return back()->with('status', 'review-deleted');
    }

    public function like(Request $request, Title $title, Review $review): RedirectResponse
    {
        abort_unless($review->title_id === $title->id, 404);

        $review->likes()->syncWithoutDetaching([$request->user()->id]);

        return back();
    }

    public function unlike(Request $request, Title $title, Review $review): RedirectResponse
    {
        abort_unless($review->title_id === $title->id, 404);

        $review->likes()->detach($request->user()->id);

        return back();
    }

    /**
     * A root-level review is one per user per title and can carry an
     * optional star score. Resubmitting updates the existing one.
     */
    private function storeOrUpdateRootReview(Request $request, Title $title, array $validated): void
    {
        Review::updateOrCreate(
            ['user_id' => $request->user()->id, 'title_id' => $title->id, 'parent_id' => null],
            ['depth' => 1, 'score' => $validated['score'] ?? null, 'body' => $validated['body']]
        );
    }

    /**
     * A reply is just discussion on a review — no score, and unlike the root
     * review a user can post as many as they like.
     */
    private function storeReply(Request $request, Title $title, Review $parent, string $body): void
    {
        $review = Review::create([
            'user_id' => $request->user()->id,
            'title_id' => $title->id,
            'parent_id' => $parent->id,
            'depth' => min($parent->depth + 1, 3),
            'score' => null,
            'body' => $body,
        ]);

        if ($parent->user_id === $request->user()->id) {
            return;
        }

        $notifyPref = $parent->user->notificationSetting;

        if ($notifyPref && ! $notifyPref->notify_on_review_reply) {
            return;
        }

        Notification::create([
            'user_id' => $parent->user_id,
            'type' => 'review_reply',
            'data' => [
                'title_id' => $title->id,
                'title_slug' => $title->slug,
                'title_name' => $title->title,
                'review_id' => $review->id,
                'from_username' => $request->user()->username,
            ],
        ]);
    }
}
