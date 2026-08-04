<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TitleController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type')->toString();
        $search = $request->string('search')->toString();

        $titles = Title::query()
            ->where('status', 'accepted')
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($search, fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->withCount(['reviews as reviews_count' => fn ($query) => $query->whereNull('parent_id')])
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('catalogue.index', [
            'titles' => $titles,
            'type' => $type,
            'search' => $search,
        ]);
    }

    public function show(Title $title): View
    {
        abort_unless($title->status === 'accepted', 404);

        $title->load([
            'genres',
            'platforms',
            'reviews' => fn ($query) => $query->whereNull('parent_id')->with(['user', 'likes', 'replies.user', 'replies.likes', 'replies.replies.user', 'replies.replies.likes'])->latest(),
        ]);

        $blockedIds = auth()->check() ? auth()->user()->blockedUsers()->pluck('users.id') : collect();

        $userReview = auth()->check() ? $title->reviews->firstWhere('user_id', auth()->id()) : null;
        $watchlistEntry = auth()->check() ? $title->watchlistEntries()->where('user_id', auth()->id())->first() : null;
        $gamingEntry = auth()->check() ? $title->gamingEntries()->where('user_id', auth()->id())->first() : null;
        $isFavourited = auth()->check() ? $title->favouritedBy()->where('user_id', auth()->id())->exists() : false;

        return view('catalogue.show', [
            'title' => $title,
            'userReview' => $userReview,
            'watchlistEntry' => $watchlistEntry,
            'gamingEntry' => $gamingEntry,
            'isFavourited' => $isFavourited,
            'blockedIds' => $blockedIds,
        ]);
    }
}
