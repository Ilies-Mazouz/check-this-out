<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\WatchlistEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WatchlistController extends Controller
{
    public function mine(Request $request): View
    {
        $entries = $request->user()->watchlistEntries()->with('title')->latest()->get()->groupBy('status');

        return view('lists.watchlist', ['entries' => $entries]);
    }

    public function update(Request $request, Title $title): RedirectResponse
    {
        abort_unless(in_array($title->type, ['movie', 'series', 'anime'], true), 422);

        $validated = $request->validate([
            'status' => ['required', 'in:want_to_watch,watching,completed,dropped'],
        ]);

        WatchlistEntry::updateOrCreate(
            ['user_id' => $request->user()->id, 'title_id' => $title->id],
            $validated
        );

        return back()->with('status', 'watchlist-updated');
    }

    public function destroy(Request $request, Title $title): RedirectResponse
    {
        WatchlistEntry::where('user_id', $request->user()->id)->where('title_id', $title->id)->delete();

        return back()->with('status', 'watchlist-removed');
    }
}
