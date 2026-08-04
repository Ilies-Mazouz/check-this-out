<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\NewsArticle;
use App\Models\Title;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $sevenDaysAgo = now()->subDays(7);

        return view('admin.dashboard', [
            'pendingTitles' => Title::where('status', 'pending')->count(),
            'openContactMessages' => ContactMessage::whereNull('read_at')->count(),
            'totalUsers' => User::count(),

            'catalogueTotal' => Title::where('status', 'accepted')->count(),
            'catalogueByType' => Title::where('status', 'accepted')
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'catalogueAddedThisWeek' => Title::where('status', 'accepted')->where('created_at', '>=', $sevenDaysAgo)->count(),

            'newsTotal' => NewsArticle::count(),
            'newsAddedThisWeek' => NewsArticle::where('created_at', '>=', $sevenDaysAgo)->count(),

            'lastScheduledImport' => Cache::get('last_scheduled_title_import'),
        ]);
    }
}
