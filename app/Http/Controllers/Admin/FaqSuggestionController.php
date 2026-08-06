<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqSuggestion;
use Illuminate\Http\RedirectResponse;

class FaqSuggestionController extends Controller
{
    public function resolve(FaqSuggestion $faqSuggestion): RedirectResponse
    {
        $faqSuggestion->update([
            'resolved_at' => $faqSuggestion->resolved_at ? null : now(),
        ]);

        return back();
    }

    public function destroy(FaqSuggestion $faqSuggestion): RedirectResponse
    {
        $faqSuggestion->delete();

        return back()->with('status', 'faq-suggestion-deleted');
    }
}
