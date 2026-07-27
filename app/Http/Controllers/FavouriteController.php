<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
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
