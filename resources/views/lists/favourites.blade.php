@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">My Lists</p>
        <h1 class="mt-2 font-bold font-[Fredoka] text-5xl">Favourites</h1>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($favourites as $title)
                <a href="{{ route('titles.show', $title) }}" class="rounded-xl border p-4 transition-all duration-300 hover:-translate-y-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border);">
                    <p class="font-semibold">{{ $title->title }}</p>
                    <p class="mt-1 text-xs" style="color: var(--theme-muted);">{{ ucfirst($title->type) }}</p>
                </a>
            @empty
                <p class="text-sm" style="color: var(--theme-muted);">You haven't favourited anything yet. Browse the <a href="{{ route('catalogue') }}" class="underline" style="color: var(--theme-accent);">catalogue</a> to get started.</p>
            @endforelse
        </div>
    </div>
@endsection
