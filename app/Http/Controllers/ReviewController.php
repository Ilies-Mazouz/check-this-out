<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Title;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Title $title): RedirectResponse
    {
        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:1', 'max:10'],
            'body' => ['required', 'string', 'max:3000'],
        ]);

        Review::updateOrCreate(
            ['user_id' => $request->user()->id, 'title_id' => $title->id],
            $validated
        );

        return back()->with('status', 'review-saved');
    }

    public function destroy(Request $request, Title $title, Review $review): RedirectResponse
    {
        abort_unless($review->user_id === $request->user()->id, 403);
        abort_unless($review->title_id === $title->id, 404);

        $review->delete();

        return back()->with('status', 'review-deleted');
    }
}
