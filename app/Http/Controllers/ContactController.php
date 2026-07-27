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

        $contactMessage = ContactMessage::create($validated);

        $adminEmails = User::where('is_admin', true)->pluck('email');

        if ($adminEmails->isNotEmpty()) {
            Mail::to($adminEmails->all())->send(new ContactMessageReceived($contactMessage));
        }

        return redirect()->route('contact')->with('status', 'contact-sent');
    }
}
