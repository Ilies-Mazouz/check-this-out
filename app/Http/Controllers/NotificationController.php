<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);

        $request->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return view('notifications.index', ['notifications' => $notifications]);
    }
}
