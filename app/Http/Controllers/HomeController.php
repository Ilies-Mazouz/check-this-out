<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Models\Title;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $trendingTitles = Title::where('status', 'accepted')
            ->withCount(['reviews as reviews_count' => fn ($query) => $query->whereNull('parent_id')])
            ->withAvg(['reviews as reviews_avg_score' => fn ($query) => $query->whereNull('parent_id')], 'score')
            ->get()
            ->filter(fn ($title) => $title->reviews_avg_score > 0)
            ->sortBy([
                ['reviews_avg_score', 'desc'],
                ['reviews_count', 'desc'],
            ])
            ->take(8)
            ->values();

        return view('welcome', [
            'trendingTitles' => $trendingTitles,
            'latestTitles' => Title::where('status', 'accepted')->latest()->take(8)->get(),
            'latestArticles' => NewsArticle::latest('published_at')->take(3)->get(),
            'catalogueTotal' => Title::where('status', 'accepted')->count(),
        ]);
    }
}
