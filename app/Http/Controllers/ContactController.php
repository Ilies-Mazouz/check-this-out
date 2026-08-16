<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('contact.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $contactMessage = ContactMessage::create($validated + ['user_id' => $request->user()?->id]);

        $adminEmails = User::where('is_admin', true)->pluck('email');

        if ($adminEmails->isNotEmpty()) {
            Mail::to($adminEmails->all())->send(new ContactMessageReceived($contactMessage));
        }

        return redirect()->route('contact')->with('status', 'contact-sent');
    }

    public function myMessages(Request $request): View
    {
        $messages = $request->user()->contactMessages()->latest()->paginate(15);

        return view('contact.my-messages', ['messages' => $messages]);
    }

    public function show(Request $request, ContactMessage $contactMessage): View
    {
        abort_unless($contactMessage->user_id === $request->user()->id, 403);

        return view('contact.show', ['message' => $contactMessage]);
    }
}
