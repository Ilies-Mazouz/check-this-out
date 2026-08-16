@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin</p>
            <h2 class="mt-2 font-bold font-[Fredoka] text-4xl">Contact Messages</h2>
        </div>

        @if (session('status') === 'contact-message-replied')
            <p class="rounded-xl border px-4 py-3 text-sm" style="border-color: var(--theme-border); color: var(--theme-muted);">Reply sent.</p>
        @endif

        <div class="space-y-4">
            @forelse ($messages as $message)
                <div class="rounded-tl-[1.75rem] rounded-tr-[1.75rem] rounded-br-[1.75rem] rounded-bl-md border p-6" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);" x-data="{ replying: false }">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em]" style="color: var(--theme-muted);">{{ $message->created_at->format('M j, Y H:i') }}</p>
                            <h3 class="mt-1 text-lg font-semibold">{{ $message->subject }}</h3>
                            <p class="text-sm" style="color: var(--theme-muted);">
                                {{ $message->name }} &lt;{{ $message->email }}&gt;
                                @if ($message->user)
                                    &middot; <a href="{{ route('profile.show', $message->user) }}" class="hover:underline">{{ $message->user->username }}</a>
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($message->replied_at)
                                <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase" style="border-color: var(--theme-accent); color: var(--theme-accent);">Replied</span>
                            @endif
                            @if ($message->read_at)
                                <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase" style="border-color: var(--theme-border); color: var(--theme-muted);">Read</span>
                            @else
                                <span class="rounded-full border px-3 py-1 text-xs font-semibold uppercase" style="border-color: var(--theme-accent); color: var(--theme-accent);">New</span>
                            @endif
                        </div>
                    </div>

                    <p class="mt-4 whitespace-pre-line text-sm leading-7">{{ $message->body }}</p>

                    @if ($message->admin_reply)
                        <div class="mt-4 rounded-xl border-l-4 pl-4 py-2" style="border-color: var(--theme-accent);">
                            <p class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-muted);">Your reply &middot; {{ $message->replied_at->format('M j, Y H:i') }}</p>
                            <p class="mt-1 whitespace-pre-line text-sm leading-7">{{ $message->admin_reply }}</p>
                        </div>
                    @endif

                    <div class="mt-4 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.contact-messages.toggle-read', $message) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">
                                {{ $message->read_at ? 'Mark unread' : 'Mark read' }}
                            </button>
                        </form>

                        @if ($message->user_id)
                            <button type="button" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);" @click="replying = ! replying">
                                {{ $message->admin_reply ? 'Edit reply' : 'Reply' }}
                            </button>
                        @else
                            <a href="mailto:{{ $message->email }}?subject={{ urlencode('Re: '.$message->subject) }}" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);" title="This message wasn't sent by a logged-in account, so it can't get an in-app reply — opens your device's default mail app instead. If nothing happens, copy the address above.">Reply by email</a>
                        @endif

                        <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">Delete</button>
                        </form>
                    </div>

                    @if ($message->user_id)
                        <form method="POST" action="{{ route('admin.contact-messages.reply', $message) }}" class="mt-4" x-show="replying" x-transition>
                            @csrf
                            <textarea name="admin_reply" rows="3" required minlength="2" maxlength="5000" placeholder="Write a reply — the user will get a notification with a link to it." class="w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">{{ $message->admin_reply }}</textarea>
                            <button type="submit" class="mt-2 rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-accent); color: var(--theme-accent);">Send reply</button>
                        </form>
                    @endif
                </div>
            @empty
                <p style="color: var(--theme-muted);">No contact messages yet.</p>
            @endforelse
        </div>

        {{ $messages->links() }}
    </div>
@endsection
