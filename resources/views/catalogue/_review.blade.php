@php $liked = auth()->check() && $review->likes->contains('id', auth()->id()); @endphp

<div class="rounded-xl border p-4" style="border-color: color-mix(in srgb, var(--theme-border) 70%, transparent);">
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('profile.show', $review->user) }}" class="text-sm font-semibold hover:underline">{{ $review->user->username }}</a>
        <span class="text-xs" style="color: var(--theme-muted);">{{ $review->created_at->diffForHumans() }}</span>
    </div>

    @if ($review->depth === 1)
        <div class="mt-1 text-sm">
            @if ($review->score)
                <span style="color: var(--theme-accent);">{{ str_repeat('★', $review->score) }}{{ str_repeat('☆', 5 - $review->score) }}</span>
                <span style="color: var(--theme-muted);">{{ $review->score }}/5</span>
            @else
                <span style="color: var(--theme-muted);">No rating given</span>
            @endif
        </div>
    @endif

    <p class="mt-2 text-sm leading-6">{{ $review->body }}</p>

    <div class="mt-3 flex items-center gap-4 text-xs">
        @auth
            <form method="POST" action="{{ route($liked ? 'titles.reviews.unlike' : 'titles.reviews.like', [$title, $review]) }}">
                @csrf
                @if ($liked) @method('DELETE') @endif
                <button type="submit" class="inline-flex items-center gap-1.5 font-semibold" style="color: {{ $liked ? 'var(--theme-accent)' : 'var(--theme-muted)' }};">
                    <x-icon name="heart" class="h-3.5 w-3.5" style="{{ $liked ? 'fill: currentColor;' : '' }}" />
                    {{ $review->likes->count() }}
                </button>
            </form>
        @else
            <span class="inline-flex items-center gap-1.5" style="color: var(--theme-muted);"><x-icon name="heart" class="h-3.5 w-3.5" /> {{ $review->likes->count() }}</span>
        @endauth

        @auth
            @if ($review->depth < 3 && ! $review->user->hasBlocked(auth()->user()))
                <button type="button" class="font-semibold" style="color: var(--theme-accent);" @click="replyTo === {{ $review->id }} ? replyTo = null : replyTo = {{ $review->id }}">Reply</button>
            @endif
        @endauth

        @if (auth()->id() === $review->user_id || (auth()->check() && auth()->user()->is_admin))
            <form method="POST" action="{{ route('titles.reviews.destroy', [$title, $review]) }}" onsubmit="return confirm('Delete this review?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="color: var(--theme-muted);">Delete</button>
            </form>
        @endif
    </div>

    @auth
        @if ($review->depth < 3 && ! $review->user->hasBlocked(auth()->user()))
            <form method="POST" action="{{ route('titles.reviews.store', $title) }}" class="mt-3" x-show="replyTo === {{ $review->id }}" x-transition>
                @csrf
                <input type="hidden" name="parent_id" value="{{ $review->id }}" />
                <textarea name="body" rows="2" required minlength="2" maxlength="3000" placeholder="Write a reply..." class="w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);"></textarea>
                <button type="submit" class="mt-2 rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-accent); color: var(--theme-accent);">Post reply</button>
            </form>
        @endif
    @endauth

    @if ($review->replies->isNotEmpty())
        <div class="mt-4 space-y-3 border-l pl-4" style="border-color: var(--theme-border);">
            @foreach ($review->replies->whereNotIn('user_id', $blockedIds) as $reply)
                @include('catalogue._review', ['review' => $reply, 'title' => $title, 'blockedIds' => $blockedIds])
            @endforeach
        </div>
    @endif
</div>
