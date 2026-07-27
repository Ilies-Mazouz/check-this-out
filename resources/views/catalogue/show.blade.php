@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-[280px_1fr]">
            <div>
                @if ($title->cover_image)
                    <img src="{{ asset('storage/'.$title->cover_image) }}" alt="{{ $title->title }}" class="w-full rounded-[1.5rem] object-cover" style="box-shadow: var(--theme-glow);" />
                @else
                    <div class="flex aspect-[2/3] w-full items-center justify-center rounded-[1.5rem] border text-5xl" style="border-color: var(--theme-border);">🎬</div>
                @endif

                @auth
                    <div class="mt-4 space-y-2">
                        <form method="POST" action="{{ route($isFavourited ? 'titles.favourite.destroy' : 'titles.favourite.store', $title) }}">
                            @csrf
                            @if ($isFavourited) @method('DELETE') @endif
                            <button type="submit" class="w-full rounded-xl border px-4 py-2 text-sm font-semibold uppercase tracking-[0.04em] transition-all duration-300" style="border-color: var(--theme-accent); color: {{ $isFavourited ? 'var(--theme-bg)' : 'var(--theme-accent)' }}; background: {{ $isFavourited ? 'var(--theme-accent)' : 'transparent' }};">
                                {{ $isFavourited ? '★ Favourited' : '☆ Add to Favourites' }}
                            </button>
                        </form>

                        @if (in_array($title->type, ['movie', 'series', 'anime']))
                            <form method="POST" action="{{ route('titles.watchlist.update', $title) }}">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">
                                    <option value="" disabled {{ ! $watchlistEntry ? 'selected' : '' }}>Add to watchlist...</option>
                                    @foreach (['want_to_watch' => 'Want to Watch', 'watching' => 'Watching', 'completed' => 'Completed', 'dropped' => 'Dropped'] as $value => $label)
                                        <option value="{{ $value }}" {{ $watchlistEntry?->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @else
                            <form method="POST" action="{{ route('titles.gaming.update', $title) }}">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">
                                    <option value="" disabled {{ ! $gamingEntry ? 'selected' : '' }}>Add to gaming list...</option>
                                    @foreach (['backlog' => 'Backlog', 'playing' => 'Playing', 'completed' => 'Completed', 'dropped' => 'Dropped', '100percent' => '100%'] as $value => $label)
                                        <option value="{{ $value }}" {{ $gamingEntry?->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    </div>
                @endauth
            </div>

            <div>
                <span class="text-xs font-semibold uppercase tracking-[0.2em]" style="color: var(--theme-accent);">{{ ucfirst($title->type) }}</span>
                <h1 class="mt-2 font-[Bebas_Neue] text-5xl uppercase tracking-[0.14em]">{{ $title->title }}</h1>

                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm" style="color: var(--theme-muted);">
                    @if ($title->release_date)
                        <span>{{ $title->release_date->format('Y') }}</span>
                    @endif
                    @if ($title->reviews->isNotEmpty())
                        <span>★ {{ number_format($title->reviews->avg('score'), 1) }}/10 ({{ $title->reviews->count() }})</span>
                    @endif
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($title->genres as $genre)
                        <span class="rounded-full border px-3 py-1 text-xs" style="border-color: var(--theme-border); color: var(--theme-muted);">{{ $genre->name }}</span>
                    @endforeach
                    @foreach ($title->platforms as $platform)
                        <span class="rounded-full border px-3 py-1 text-xs" style="border-color: var(--theme-border); color: var(--theme-muted);">{{ $platform->name }}</span>
                    @endforeach
                </div>

                @if ($title->synopsis)
                    <p class="mt-6 text-lg leading-8">{{ $title->synopsis }}</p>
                @endif
            </div>
        </div>

        <div class="mt-12 border-t pt-8" style="border-color: var(--theme-border);">
            <h2 class="text-2xl font-semibold">Reviews</h2>

            @auth
                <form method="POST" action="{{ route('titles.reviews.store', $title) }}" class="mt-4 space-y-3 rounded-[1.5rem] border p-5" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border);">
                    @csrf
                    <div class="flex items-center gap-3">
                        <x-input-label value="Your score" />
                        <select name="score" required class="rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ $userReview?->score === $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <textarea name="body" rows="3" required minlength="3" maxlength="3000" placeholder="Share your thoughts..." class="w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">{{ old('body', $userReview->body ?? '') }}</textarea>
                    <div class="flex items-center gap-3">
                        <x-primary-button>{{ $userReview ? 'Update review' : 'Post review' }}</x-primary-button>
                        @if ($userReview)
                            <form method="POST" action="{{ route('titles.reviews.destroy', [$title, $userReview]) }}" onsubmit="return confirm('Delete your review?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm" style="color: var(--theme-muted);">Delete</button>
                            </form>
                        @endif
                    </div>
                </form>
            @endauth

            <div class="mt-6 space-y-4">
                @forelse ($title->reviews->whereNotIn('user_id', $blockedIds) as $review)
                    <div class="rounded-xl border p-4" style="border-color: color-mix(in srgb, var(--theme-border) 70%, transparent);">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('profile.show', $review->user) }}" class="text-sm font-semibold hover:underline">{{ $review->user->username }}</a>
                            <span class="text-sm font-semibold" style="color: var(--theme-accent);">{{ $review->score }}/10</span>
                        </div>
                        <p class="mt-2 text-sm leading-6">{{ $review->body }}</p>
                    </div>
                @empty
                    <p style="color: var(--theme-muted);">No reviews yet — be the first.</p>
                @endforelse
            </div>
        </div>

        <div class="mt-12 border-t pt-8" style="border-color: var(--theme-border);" x-data="{ replyTo: null }">
            <h2 class="text-2xl font-semibold">Comments</h2>

            @auth
                <form method="POST" action="{{ route('titles.comments.store', $title) }}" class="mt-4 space-y-3 rounded-[1.5rem] border p-5" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border);">
                    @csrf
                    <textarea name="body" rows="3" required minlength="2" maxlength="2000" placeholder="Join the discussion..." class="w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);"></textarea>
                    <x-primary-button>Post comment</x-primary-button>
                </form>
            @endauth

            <div class="mt-6 space-y-4">
                @forelse ($title->comments->whereNotIn('user_id', $blockedIds) as $comment)
                    @include('catalogue._comment', ['comment' => $comment, 'title' => $title, 'blockedIds' => $blockedIds])
                @empty
                    <p style="color: var(--theme-muted);">No comments yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
