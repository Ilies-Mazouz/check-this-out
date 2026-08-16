<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(): View
    {
        $messages = ContactMessage::query()->orderByDesc('created_at')->paginate(15);

        return view('admin.contact-messages.index', ['messages' => $messages]);
    }

    public function toggleRead(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update([
            'read_at' => $contactMessage->read_at ? null : now(),
        ]);

        return back();
    }

    public function reply(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $validated = $request->validate([
            'admin_reply' => ['required', 'string', 'max:5000'],
        ]);

        $isFirstReply = $contactMessage->replied_at === null;

        $contactMessage->update([
            'admin_reply' => $validated['admin_reply'],
            'replied_at' => now(),
            'read_at' => $contactMessage->read_at ?? now(),
        ]);

        if ($isFirstReply && $contactMessage->user_id) {
            Notification::create([
                'user_id' => $contactMessage->user_id,
                'type' => 'contact_reply',
                'data' => [
                    'contact_message_id' => $contactMessage->id,
                    'subject' => $contactMessage->subject,
                ],
            ]);
        }

        return back()->with('status', 'contact-message-replied');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return back()->with('status', 'contact-message-deleted');
    }
}
