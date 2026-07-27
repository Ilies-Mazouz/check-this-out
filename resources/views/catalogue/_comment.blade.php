@php $liked = auth()->check() && $comment->likes->contains('id', auth()->id()); @endphp

<div class="rounded-xl border p-4" style="border-color: color-mix(in srgb, var(--theme-border) 70%, transparent);">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('profile.show', $comment->user) }}" class="text-sm font-semibold hover:underline">{{ $comment->user->username }}</a>
        <span class="text-xs" style="color: var(--theme-muted);">{{ $comment->created_at->diffForHumans() }}</span>
    </div>
    <p class="mt-2 text-sm leading-6">{{ $comment->body }}</p>

    <div class="mt-3 flex items-center gap-4 text-xs">
        @auth
            <form method="POST" action="{{ route($liked ? 'titles.comments.unlike' : 'titles.comments.like', [$title, $comment]) }}">
                @csrf
                @if ($liked) @method('DELETE') @endif
                <button type="submit" class="font-semibold" style="color: {{ $liked ? 'var(--theme-accent)' : 'var(--theme-muted)' }};">
                    👍 {{ $comment->likes->count() }}
                </button>
            </form>
        @else
            <span style="color: var(--theme-muted);">👍 {{ $comment->likes->count() }}</span>
        @endauth

        @auth
            @if ($comment->depth < 3)
                <button type="button" class="font-semibold" style="color: var(--theme-accent);" @click="replyTo === {{ $comment->id }} ? replyTo = null : replyTo = {{ $comment->id }}">Reply</button>
            @endif
        @endauth

        @if (auth()->id() === $comment->user_id || (auth()->check() && auth()->user()->is_admin))
            <form method="POST" action="{{ route('titles.comments.destroy', [$title, $comment]) }}" onsubmit="return confirm('Delete this comment?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="color: var(--theme-muted);">Delete</button>
            </form>
        @endif
    </div>

    @auth
        @if ($comment->depth < 3)
            <form method="POST" action="{{ route('titles.comments.store', $title) }}" class="mt-3" x-show="replyTo === {{ $comment->id }}" x-transition>
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}" />
                <textarea name="body" rows="2" required minlength="2" maxlength="2000" placeholder="Write a reply..." class="w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);"></textarea>
                <button type="submit" class="mt-2 rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-accent); color: var(--theme-accent);">Post reply</button>
            </form>
        @endif
    @endauth

    @if ($comment->replies->isNotEmpty())
        <div class="mt-4 space-y-3 border-l pl-4" style="border-color: var(--theme-border);">
            @foreach ($comment->replies->whereNotIn('user_id', $blockedIds) as $reply)
                @include('catalogue._comment', ['comment' => $reply, 'title' => $title, 'blockedIds' => $blockedIds])
            @endforeach
        </div>
    @endif
</div>
