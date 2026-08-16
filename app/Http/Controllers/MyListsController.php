<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MyListsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('lists.index', [
            'watchlistCount' => $user->watchlistEntries()->count(),
            'gamingCount' => $user->gamingEntries()->count(),
            'favouritesCount' => $user->favourites()->count(),
        ]);
    }
}
