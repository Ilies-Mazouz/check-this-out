@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Contact</p>
        <h1 class="mt-2 font-bold font-[Fredoka] text-5xl">{{ $message->subject }}</h1>
        <p class="mt-2 text-sm" style="color: var(--theme-muted);">Sent {{ $message->created_at->format('F j, Y \a\t H:i') }}</p>

        <div class="mt-8 rounded-tl-[1.75rem] rounded-tr-[1.75rem] rounded-br-[1.75rem] rounded-bl-md border p-6 sm:p-8" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <p class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-muted);">Your message</p>
            <p class="mt-2 whitespace-pre-line text-sm leading-7">{{ $message->body }}</p>

            @if ($message->admin_reply)
                <div class="mt-6 border-t pt-6" style="border-color: var(--theme-border);">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-accent);">Answer &middot; {{ $message->replied_at->format('F j, Y \a\t H:i') }}</p>
                    <p class="mt-2 whitespace-pre-line text-sm leading-7">{{ $message->admin_reply }}</p>
                </div>
            @else
                <div class="mt-6 border-t pt-6" style="border-color: var(--theme-border);">
                    <p class="text-sm" style="color: var(--theme-muted);">No answer yet — we'll notify you here once someone replies.</p>
                </div>
            @endif
        </div>

        <a href="{{ route('contact.messages.index') }}" class="mt-6 inline-block text-sm font-semibold" style="color: var(--theme-accent);">&larr; Back to your messages</a>
    </div>
@endsection
