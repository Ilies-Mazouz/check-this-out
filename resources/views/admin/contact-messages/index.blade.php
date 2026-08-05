@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin</p>
            <h2 class="mt-2 font-bold font-[Fredoka] text-4xl">Contact Messages</h2>
        </div>

        <div class="space-y-4">
            @forelse ($messages as $message)
                <div class="rounded-tl-[1.75rem] rounded-tr-[1.75rem] rounded-br-[1.75rem] rounded-bl-md border p-6" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em]" style="color: var(--theme-muted);">{{ $message->created_at->format('M j, Y H:i') }}</p>
                            <h3 class="mt-1 text-lg font-semibold">{{ $message->subject }}</h3>
                            <p class="text-sm" style="color: var(--theme-muted);">{{ $message->name }} &lt;{{ $message->email }}&gt;</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($message->read_at)
                                <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase" style="border-color: var(--theme-border); color: var(--theme-muted);">Read</span>
                            @else
                                <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase" style="border-color: var(--theme-accent); color: var(--theme-accent);">New</span>
                            @endif
                        </div>
                    </div>

                    <p class="mt-4 whitespace-pre-line text-sm leading-7">{{ $message->body }}</p>

                    <div class="mt-4 flex gap-2">
                        <form method="POST" action="{{ route('admin.contact-messages.toggle-read', $message) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">
                                {{ $message->read_at ? 'Mark unread' : 'Mark read' }}
                            </button>
                        </form>
                        <a href="mailto:{{ $message->email }}" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">Reply by email</a>
                        <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p style="color: var(--theme-muted);">No contact messages yet.</p>
            @endforelse
        </div>

        {{ $messages->links() }}
    </div>
@endsection
