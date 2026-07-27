<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function store(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->id === $user->id, 422);

        $request->user()->blockedUsers()->syncWithoutDetaching([$user->id]);

        return back()->with('status', 'user-blocked');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $request->user()->blockedUsers()->detach($user->id);

        return back()->with('status', 'user-unblocked');
    }
}
