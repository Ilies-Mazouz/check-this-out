@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <a href="{{ route('news.index') }}" class="text-sm font-semibold uppercase tracking-[0.1em]" style="color: var(--theme-accent);">&larr; Back to news</a>

        <p class="mt-6 text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">{{ $article->published_at->format('F j, Y') }}</p>
        <h1 class="mt-2 font-bold font-[Fredoka] text-5xl">{{ $article->title }}</h1>
        @if ($article->user)
            <p class="mt-2 text-sm" style="color: var(--theme-muted);">By {{ $article->user->username }}</p>
        @endif

        @if ($article->cover_image)
            <x-cover-image :src="$article->cover_image" :alt="$article->title" class="mt-8 h-[420px] w-full rounded-tl-[1.75rem] rounded-tr-[1.75rem] rounded-br-[1.75rem] rounded-bl-md object-cover" />
        @endif

        <div class="prose prose-invert mt-8 max-w-none text-lg leading-8" style="color: var(--theme-text);">
            {!! nl2br(e($article->body)) !!}
        </div>

        @if ($article->source_url)
            <p class="mt-8 border-t pt-4 text-sm" style="border-color: var(--theme-border); color: var(--theme-muted);">Originally reported at <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" class="underline hover:no-underline">{{ parse_url($article->source_url, PHP_URL_HOST) }}</a></p>
        @endif

        <div class="mt-12 border-t pt-8" style="border-color: var(--theme-border);">
            <h2 class="text-2xl font-semibold">Comments</h2>

            @auth
                <form method="POST" action="{{ route('news.comments.store', $article) }}" class="mt-4 space-y-3">
                    @csrf
                    <textarea name="body" rows="3" required minlength="2" maxlength="2000" placeholder="Say something about this article..." class="w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);"></textarea>
                    <x-primary-button>Post comment</x-primary-button>
                </form>
            @else
                <p class="mt-4 text-sm" style="color: var(--theme-muted);"><a href="{{ route('login') }}" class="underline">Log in</a> to leave a comment.</p>
            @endauth

            <div class="mt-6 space-y-3">
                @forelse ($comments as $comment)
                    <div class="rounded-xl border p-4" style="border-color: color-mix(in srgb, var(--theme-border) 70%, transparent);">
                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ route('profile.show', $comment->user) }}" class="text-sm font-semibold hover:underline">{{ $comment->user->username }}</a>
                            <span class="text-xs" style="color: var(--theme-muted);">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mt-2 text-sm leading-6">{{ $comment->body }}</p>

                        @if (auth()->id() === $comment->user_id || (auth()->check() && auth()->user()->is_admin))
                            <form method="POST" action="{{ route('news.comments.destroy', [$article, $comment]) }}" onsubmit="return confirm('Delete this comment?');" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs" style="color: var(--theme-muted);">Delete</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p style="color: var(--theme-muted);">No comments yet — be the first.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
