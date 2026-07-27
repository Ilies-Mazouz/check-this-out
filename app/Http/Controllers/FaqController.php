<?php

namespace App\Http\Controllers;

use App\Models\FaqCategory;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $categories = FaqCategory::with(['faqItems' => fn ($query) => $query->orderBy('order')])
            ->orderBy('order')
            ->get();

        return view('faq.index', ['categories' => $categories]);
    }
}
