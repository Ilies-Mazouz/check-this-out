@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <a href="{{ route('news.index') }}" class="text-sm font-semibold uppercase tracking-[0.1em]" style="color: var(--theme-accent);">&larr; Back to news</a>

        <p class="mt-6 text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">{{ $article->published_at->format('F j, Y') }}</p>
        <h1 class="mt-2 font-[Bebas_Neue] text-5xl uppercase tracking-[0.18em]">{{ $article->title }}</h1>
        @if ($article->user)
            <p class="mt-2 text-sm" style="color: var(--theme-muted);">By {{ $article->user->username }}</p>
        @endif

        @if ($article->cover_image)
            <img src="{{ asset('storage/'.$article->cover_image) }}" alt="{{ $article->title }}" class="mt-8 w-full rounded-[1.75rem] object-cover" style="max-height: 420px;" />
        @endif

        <div class="prose prose-invert mt-8 max-w-none text-lg leading-8" style="color: var(--theme-text);">
            {!! nl2br(e($article->body)) !!}
        </div>
    </div>
@endsection
