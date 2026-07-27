@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Discover</p>
                <h1 class="mt-2 font-[Bebas_Neue] text-5xl uppercase tracking-[0.18em]">Catalogue</h1>
            </div>

            @auth
                <a href="{{ route('titles.submit') }}" class="inline-flex h-11 items-center rounded-xl border px-5 text-sm font-semibold uppercase tracking-[0.04em]" style="border-color: var(--theme-accent); color: var(--theme-accent);">+ Submit a title</a>
            @endauth
        </div>

        <form method="GET" action="{{ route('catalogue') }}" class="mt-8 flex flex-wrap gap-3">
            <select name="type" class="rounded-xl border px-4 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">
                <option value="">All types</option>
                @foreach (['movie' => 'Movies', 'series' => 'Series', 'anime' => 'Anime', 'game' => 'Games'] as $value => $label)
                    <option value="{{ $value }}" @selected($type === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search titles..." class="min-w-[220px] flex-1 rounded-xl border px-4 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);" />
            <button type="submit" class="rounded-xl border px-4 py-2 text-sm font-semibold" style="border-color: var(--theme-border);">Filter</button>
        </form>

        @if ($titles->isEmpty())
            <p class="mt-10 text-lg" style="color: var(--theme-muted);">No titles found yet.</p>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($titles as $catalogueTitle)
                    <a href="{{ route('titles.show', $catalogueTitle) }}" class="group overflow-hidden rounded-[1.5rem] border transition-all duration-300 hover:-translate-y-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                        @if ($catalogueTitle->cover_image)
                            <img src="{{ asset('storage/'.$catalogueTitle->cover_image) }}" alt="{{ $catalogueTitle->title }}" class="h-56 w-full object-cover" />
                        @else
                            <div class="flex h-56 w-full items-center justify-center border-b text-4xl" style="border-color: var(--theme-border);">🎬</div>
                        @endif
                        <div class="p-4">
                            <span class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-accent);">{{ ucfirst($catalogueTitle->type) }}</span>
                            <h2 class="mt-1 font-semibold transition-all duration-300 group-hover:text-[color:var(--theme-accent)]">{{ $catalogueTitle->title }}</h2>
                            <p class="mt-1 text-xs" style="color: var(--theme-muted);">{{ $catalogueTitle->reviews_count }} review(s)</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $titles->links() }}</div>
        @endif
    </div>
@endsection
