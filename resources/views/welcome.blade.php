@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] border p-8 sm:p-14" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Movies · Series · Anime · Games</p>
            <h1 class="mt-4 max-w-3xl font-[Bebas_Neue] text-6xl uppercase leading-[0.95] tracking-[0.06em]">Check This Out</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8" style="color: var(--theme-muted);">Track what you're watching and playing, rate and review titles, and see what the community is recommending next.</p>

            <div class="mt-8 flex flex-wrap gap-4">
                @guest
                    <a href="{{ route('register') }}" class="inline-flex h-12 items-center rounded-xl px-6 text-sm font-semibold uppercase tracking-[0.04em] transition-all duration-300 hover:scale-105" style="background: var(--theme-accent); color: var(--theme-bg);">Get Started</a>
                    <a href="{{ route('login') }}" class="inline-flex h-12 items-center rounded-xl border px-6 text-sm font-semibold uppercase tracking-[0.04em] transition-all duration-300 hover:bg-[color:var(--theme-accent)] hover:text-[color:var(--theme-bg)]" style="border-color: var(--theme-accent); color: var(--theme-accent);">Sign In</a>
                @else
                    <a href="{{ route('dashboard') }}" class="inline-flex h-12 items-center rounded-xl px-6 text-sm font-semibold uppercase tracking-[0.04em] transition-all duration-300 hover:scale-105" style="background: var(--theme-accent); color: var(--theme-bg);">Go to Dashboard</a>
                @endguest
                <a href="{{ route('catalogue') }}" class="inline-flex h-12 items-center rounded-xl border px-6 text-sm font-semibold uppercase tracking-[0.04em] transition-all duration-300 hover:bg-[color:var(--theme-accent)] hover:text-[color:var(--theme-bg)]" style="border-color: var(--theme-border); color: var(--theme-text);">Browse Catalogue</a>
            </div>
        </div>

        <div class="mt-16 flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-[Bebas_Neue] text-3xl uppercase tracking-[0.12em]">Recently Added</h2>
            <a href="{{ route('catalogue') }}" class="text-sm font-semibold uppercase tracking-[0.04em]" style="color: var(--theme-accent);">View all &rarr;</a>
        </div>

        @if ($latestTitles->isEmpty())
            <p class="mt-6 text-sm" style="color: var(--theme-muted);">No titles in the catalogue yet — be the first to <a href="{{ route('titles.submit') }}" class="underline">submit one</a>.</p>
        @else
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($latestTitles as $title)
                    <a href="{{ route('titles.show', $title) }}" class="group overflow-hidden rounded-[1.5rem] border transition-all duration-300 hover:-translate-y-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                        <x-cover-image :src="$title->cover_image" :alt="$title->title" :icon="['movie' => '🎬', 'series' => '📺', 'anime' => '🎌', 'game' => '🎮'][$title->type] ?? '🎬'" class="h-56 w-full object-cover" />
                        <div class="p-4">
                            <span class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-accent);">{{ ucfirst($title->type) }}</span>
                            <h3 class="mt-1 font-semibold transition-all duration-300 group-hover:text-[color:var(--theme-accent)]">{{ $title->title }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-16 flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-[Bebas_Neue] text-3xl uppercase tracking-[0.12em]">Latest News</h2>
            <a href="{{ route('news.index') }}" class="text-sm font-semibold uppercase tracking-[0.04em]" style="color: var(--theme-accent);">View all &rarr;</a>
        </div>

        @if ($latestArticles->isEmpty())
            <p class="mt-6 text-sm" style="color: var(--theme-muted);">No news articles published yet.</p>
        @else
            <div class="mt-6 grid gap-6 md:grid-cols-3">
                @foreach ($latestArticles as $article)
                    <a href="{{ route('news.show', $article) }}" class="group rounded-[1.75rem] border p-5 transition-all duration-300 hover:-translate-y-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em]" style="color: var(--theme-muted);">{{ $article->published_at->format('F j, Y') }}</p>
                        <h3 class="mt-2 text-xl font-semibold transition-all duration-300 group-hover:text-[color:var(--theme-accent)]">{{ $article->title }}</h3>
                        <p class="mt-2 line-clamp-3 text-sm" style="color: var(--theme-muted);">{{ Str::limit(strip_tags($article->body), 140) }}</p>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-16 grid gap-6 sm:grid-cols-2">
            <a href="{{ route('faq.index') }}" class="rounded-[1.75rem] border p-6 transition-all duration-300 hover:-translate-y-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                <h3 class="text-xl font-semibold">Have a question?</h3>
                <p class="mt-2 text-sm" style="color: var(--theme-muted);">Check the FAQ &rarr;</p>
            </a>
            <a href="{{ route('contact') }}" class="rounded-[1.75rem] border p-6 transition-all duration-300 hover:-translate-y-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                <h3 class="text-xl font-semibold">Get in touch</h3>
                <p class="mt-2 text-sm" style="color: var(--theme-muted);">Contact us &rarr;</p>
            </a>
        </div>
    </div>
@endsection
