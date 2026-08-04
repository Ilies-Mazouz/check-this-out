<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'notify_on_review_reply' => ['sometimes', 'boolean'],
        ]);

        $request->user()->notificationSetting()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['notify_on_review_reply' => $request->boolean('notify_on_review_reply')]
        );

        return back()->with('status', 'notification-settings-updated');
    }
}
