<?php

namespace App\Http\Controllers;

use App\Models\GamingEntry;
use App\Models\Title;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GamingEntryController extends Controller
{
    public function mine(Request $request): View
    {
        $entries = $request->user()->gamingEntries()->with('title')->latest()->get()->groupBy('status');

        return view('lists.gaming', ['entries' => $entries]);
    }

    public function update(Request $request, Title $title): RedirectResponse
    {
        abort_unless($title->type === 'game', 422);

        $validated = $request->validate([
            'status' => ['required', 'in:backlog,playing,completed,dropped,100percent'],
        ]);

        GamingEntry::updateOrCreate(
            ['user_id' => $request->user()->id, 'title_id' => $title->id],
            $validated
        );

        return back()->with('status', 'gaming-updated');
    }

    public function destroy(Request $request, Title $title): RedirectResponse
    {
        GamingEntry::where('user_id', $request->user()->id)->where('title_id', $title->id)->delete();

        return back()->with('status', 'gaming-removed');
    }
}
