@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <a href="{{ route('news.index') }}" class="text-sm font-semibold uppercase tracking-[0.1em]" style="color: var(--theme-accent);">&larr; Back to news</a>

        <p class="mt-6 text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">{{ $article->published_at->format('F j, Y') }}</p>
        <h1 class="mt-2 font-[Bebas_Neue] text-5xl uppercase tracking-[0.18em]">{{ $article->title }}</h1>
        @if ($article->user)
            <p class="mt-2 text-sm" style="color: var(--theme-muted);">By {{ $article->user->username }}</p>
        @endif

        <x-cover-image :src="$article->cover_image" :alt="$article->title" icon="📰" class="mt-8 h-[420px] w-full rounded-[1.75rem] object-cover text-6xl" />

        <div class="prose prose-invert mt-8 max-w-none text-lg leading-8" style="color: var(--theme-text);">
            {!! nl2br(e($article->body)) !!}
        </div>

        @if ($article->source_url)
            <p class="mt-8 border-t pt-4 text-sm" style="border-color: var(--theme-border); color: var(--theme-muted);">Originally reported at <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" class="underline hover:no-underline">{{ parse_url($article->source_url, PHP_URL_HOST) }}</a></p>
        @endif
    </div>
@endsection
