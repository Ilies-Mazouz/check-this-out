<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavouriteController extends Controller
{
    public function mine(Request $request): View
    {
        $favourites = $request->user()->favourites()->latest('favourites.created_at')->get();

        return view('lists.favourites', ['favourites' => $favourites]);
    }

    public function store(Request $request, Title $title): RedirectResponse
    {
        $request->user()->favourites()->syncWithoutDetaching([$title->id]);

        return back()->with('status', 'favourite-added');
    }

    public function destroy(Request $request, Title $title): RedirectResponse
    {
        $request->user()->favourites()->detach($title->id);

        return back()->with('status', 'favourite-removed');
    }
}
