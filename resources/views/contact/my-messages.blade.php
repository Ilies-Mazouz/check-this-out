@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Contact</p>
        <h1 class="mt-2 font-bold font-[Fredoka] text-5xl">Your Messages</h1>
        <p class="mt-3" style="color: var(--theme-muted);">Everything you've sent through the <a href="{{ route('contact') }}" class="underline" style="color: var(--theme-accent);">contact form</a>, and any answers.</p>

        <div class="mt-8 space-y-3">
            @forelse ($messages as $message)
                <a href="{{ route('contact.messages.show', $message) }}" class="block rounded-tl-[1.5rem] rounded-tr-[1.5rem] rounded-br-[1.5rem] rounded-bl-md border p-5 transition-all duration-300 hover:-translate-y-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="font-semibold">{{ $message->subject }}</h2>
                        @if ($message->admin_reply)
                            <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase" style="border-color: var(--theme-accent); color: var(--theme-accent);">Answered</span>
                        @else
                            <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase" style="border-color: var(--theme-border); color: var(--theme-muted);">Awaiting reply</span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs" style="color: var(--theme-muted);">{{ $message->created_at->format('M j, Y') }}</p>
                </a>
            @empty
                <p style="color: var(--theme-muted);">You haven't sent any messages yet.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $messages->links() }}</div>
    </div>
@endsection
