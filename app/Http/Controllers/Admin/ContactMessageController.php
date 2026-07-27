<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
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

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return back()->with('status', 'contact-message-deleted');
    }
}
