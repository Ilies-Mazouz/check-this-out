@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-[280px_1fr]">
            <div>
                <x-cover-image :src="$title->cover_image" :alt="$title->title" :icon="['movie' => '🎬', 'series' => '📺', 'anime' => '🎌', 'game' => '🎮'][$title->type] ?? '🎬'" class="aspect-[2/3] w-full rounded-[1.5rem] object-cover text-5xl" style="box-shadow: var(--theme-glow);" />

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
                        <span>★ {{ number_format($title->reviews->avg('score'), 1) }}/5 ({{ $title->reviews->count() }})</span>
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

                @if ($title->sourceUrl())
                    <p class="mt-4 text-xs" style="color: var(--theme-muted);">Data from <a href="{{ $title->sourceUrl() }}" target="_blank" rel="noopener noreferrer" class="underline hover:no-underline">{{ $title->sourceLabel() }}</a></p>
                @endif
            </div>
        </div>

        <div class="mt-12 border-t pt-8" style="border-color: var(--theme-border);" x-data="{ replyTo: null }">
            <h2 class="text-2xl font-semibold">Reviews</h2>

            @auth
                <div class="mt-4 rounded-[1.5rem] border p-5" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border);">
                    <form method="POST" action="{{ route('titles.reviews.store', $title) }}" class="space-y-3">
                        @csrf
                        <div x-data="{ score: {{ $userReview->score ?? 'null' }}, hover: null }">
                            <input type="hidden" name="score" :value="score ?? ''">
                            <x-input-label value="Your rating (optional)" />
                            <div class="mt-1 flex items-center gap-1">
                                <template x-for="i in 5" :key="i">
                                    <button type="button" @click="score = (score === i ? null : i)" @mouseenter="hover = i" @mouseleave="hover = null" class="text-3xl leading-none transition-all duration-150" :style="`color: ${(hover ?? score) >= i ? 'var(--theme-accent)' : 'var(--theme-border)'}`">★</button>
                                </template>
                                <button type="button" @click="score = null" x-show="score" class="ml-2 text-xs underline" style="color: var(--theme-muted);">Clear</button>
                                <span class="ml-2 text-sm" style="color: var(--theme-muted);" x-text="score ? score + '/5' : 'No rating'"></span>
                            </div>
                        </div>
                        <textarea name="body" rows="3" required minlength="3" maxlength="3000" placeholder="Share your thoughts..." class="w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">{{ old('body', $userReview->body ?? '') }}</textarea>
                        <x-primary-button>{{ $userReview ? 'Update review' : 'Post review' }}</x-primary-button>
                    </form>

                    @if ($userReview)
                        <form method="POST" action="{{ route('titles.reviews.destroy', [$title, $userReview]) }}" onsubmit="return confirm('Delete your review?');" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm" style="color: var(--theme-muted);">Delete your review</button>
                        </form>
                    @endif
                </div>
            @endauth

            <div class="mt-6 space-y-4">
                @forelse ($title->reviews->whereNotIn('user_id', $blockedIds) as $review)
                    @include('catalogue._review', ['review' => $review, 'title' => $title, 'blockedIds' => $blockedIds])
                @empty
                    <p style="color: var(--theme-muted);">No reviews yet — be the first.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
