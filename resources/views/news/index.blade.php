@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Latest</p>
        <h1 class="mt-2 font-[Bebas_Neue] text-5xl uppercase tracking-[0.18em]">News</h1>

        @if ($articles->isEmpty())
            <p class="mt-8 text-lg" style="color: var(--theme-muted);">No news articles have been published yet.</p>
        @else
            <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($articles as $article)
                    <a href="{{ route('news.show', $article) }}" class="group rounded-[1.75rem] border p-5 transition-all duration-300 hover:-translate-y-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                        <x-cover-image :src="$article->cover_image" :alt="$article->title" icon="📰" class="mb-4 h-44 w-full rounded-xl object-cover" />
                        <p class="text-xs font-semibold uppercase tracking-[0.2em]" style="color: var(--theme-muted);">{{ $article->published_at->format('F j, Y') }}</p>
                        <h2 class="mt-2 text-xl font-semibold transition-all duration-300 group-hover:text-[color:var(--theme-accent)]">{{ $article->title }}</h2>
                        <p class="mt-2 line-clamp-3 text-sm" style="color: var(--theme-muted);">{{ Str::limit(strip_tags($article->body), 140) }}</p>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $articles->links() }}</div>
        @endif
    </div>
@endsection
