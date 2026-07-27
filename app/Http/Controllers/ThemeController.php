<?php

namespace App\Http\Controllers;

use App\Services\ThemeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    public function update(Request $request, ThemeService $themeService): RedirectResponse
    {
        $validated = $request->validate([
            'theme' => ['required', Rule::in(array_keys($themeService->themes()))],
        ]);

        $request->user()->forceFill([
            'theme' => $validated['theme'],
        ])->save();

        return back();
    }
}