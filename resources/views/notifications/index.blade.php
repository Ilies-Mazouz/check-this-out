@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Updates</p>
        <h1 class="mt-2 font-bold font-[Fredoka] text-5xl">Notifications</h1>

        <div class="mt-8 space-y-3">
            @forelse ($notifications as $notification)
                <div class="rounded-xl border p-4" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border);">
                    @switch($notification->type)
                        @case('submission_accepted')
                            <p>Your submission <a href="{{ route('titles.show', ['title' => $notification->data['title_slug']]) }}" class="font-semibold hover:underline">{{ $notification->data['title_name'] }}</a> was approved and is now live.</p>
                            @break
                        @case('submission_rejected')
                            <p>Your submission <span class="font-semibold">{{ $notification->data['title_name'] }}</span> was rejected.</p>
                            @if (!empty($notification->data['rejection_reason']))
                                <p class="mt-1 text-sm" style="color: var(--theme-muted);">Reason: {{ $notification->data['rejection_reason'] }}</p>
                            @endif
                            @break
                        @case('review_reply')
                            <p><span class="font-semibold">{{ $notification->data['from_username'] }}</span> replied to your review on <a href="{{ route('titles.show', ['title' => $notification->data['title_slug']]) }}" class="font-semibold hover:underline">{{ $notification->data['title_name'] }}</a>.</p>
                            @break
                        @case('contact_reply')
                            <p>You got an answer to your message <a href="{{ route('contact.messages.show', $notification->data['contact_message_id']) }}" class="font-semibold hover:underline">"{{ $notification->data['subject'] }}"</a>.</p>
                            @break
                        @default
                            <p>{{ $notification->type }}</p>
                    @endswitch
                    <p class="mt-2 text-xs" style="color: var(--theme-muted);">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p style="color: var(--theme-muted);">No notifications yet.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $notifications->links() }}</div>
    </div>
@endsection
