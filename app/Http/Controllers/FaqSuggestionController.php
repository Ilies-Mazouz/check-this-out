<?php

namespace App\Http\Controllers;

use App\Models\FaqSuggestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FaqSuggestionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
        ]);

        FaqSuggestion::create([
            'user_id' => $request->user()->id,
            'question' => $validated['question'],
        ]);

        return back()->with('status', 'faq-suggestion-sent');
    }
}
