@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">My Lists</p>
        <h1 class="mt-2 font-bold font-[Fredoka] text-5xl">Choose a List</h1>
        <p class="mt-3" style="color: var(--theme-muted);">Everything you're tracking, in one place.</p>

        <div class="mt-10 grid gap-6 sm:grid-cols-3">
            <a href="{{ route('watchlist.mine') }}" class="group overflow-hidden rounded-tl-[1.5rem] rounded-tr-[1.5rem] rounded-br-[1.5rem] rounded-bl-md border p-6 transition-all duration-300 hover:-translate-y-1 hover:rotate-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                <x-icon name="calendar" class="h-8 w-8" style="color: var(--theme-accent);" />
                <h2 class="mt-4 text-xl font-semibold transition-all duration-300 group-hover:text-[color:var(--theme-accent)]">Watchlist</h2>
                <p class="mt-1 text-sm" style="color: var(--theme-muted);">Movies, series &amp; anime &mdash; {{ $watchlistCount }} item(s)</p>
            </a>

            <a href="{{ route('gaming.mine') }}" class="group overflow-hidden rounded-tl-[1.5rem] rounded-tr-[1.5rem] rounded-br-[1.5rem] rounded-bl-md border p-6 transition-all duration-300 hover:-translate-y-1 hover:-rotate-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                <x-icon name="flame" class="h-8 w-8" style="color: var(--theme-accent);" />
                <h2 class="mt-4 text-xl font-semibold transition-all duration-300 group-hover:text-[color:var(--theme-accent)]">Gaming List</h2>
                <p class="mt-1 text-sm" style="color: var(--theme-muted);">Games &mdash; {{ $gamingCount }} item(s)</p>
            </a>

            <a href="{{ route('favourites.mine') }}" class="group overflow-hidden rounded-tl-[1.5rem] rounded-tr-[1.5rem] rounded-br-[1.5rem] rounded-bl-md border p-6 transition-all duration-300 hover:-translate-y-1 hover:rotate-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                <x-icon name="heart" class="h-8 w-8" style="color: var(--theme-accent);" />
                <h2 class="mt-4 text-xl font-semibold transition-all duration-300 group-hover:text-[color:var(--theme-accent)]">Favourites</h2>
                <p class="mt-1 text-sm" style="color: var(--theme-muted);">All types &mdash; {{ $favouritesCount }} item(s)</p>
            </a>
        </div>
    </div>
@endsection
