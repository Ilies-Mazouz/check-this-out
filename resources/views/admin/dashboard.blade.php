@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        <div class="rounded-[2rem] border p-8" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin overview</p>
            <h2 class="mt-4 font-[Bebas_Neue] text-5xl uppercase tracking-[0.18em]">Admin Dashboard</h2>
            <p class="mt-4 max-w-3xl text-lg leading-8" style="color: var(--theme-muted);">Use the sidebar to manage the editorial and community surfaces of Check This Out.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Pending Titles', 'value' => $pendingTitles, 'route' => route('admin.titles.index', ['status' => 'pending'])],
                ['label' => 'News Articles', 'value' => $newsTotal, 'route' => route('admin.news.index')],
                ['label' => 'Open Contact Messages', 'value' => $openContactMessages, 'route' => route('admin.contact-messages.index')],
                ['label' => 'Registered Users', 'value' => $totalUsers, 'route' => route('admin.users.index')],
            ] as $card)
                <a href="{{ $card['route'] }}" class="block rounded-[1.75rem] border p-6 transition-all duration-300 hover:-translate-y-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em]" style="color: var(--theme-muted);">{{ $card['label'] }}</p>
                    <p class="mt-4 font-[Bebas_Neue] text-5xl">{{ $card['value'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-[1.75rem] border p-6" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Catalogue Library</h3>
                    <a href="{{ route('admin.titles.index', ['status' => 'accepted']) }}" class="text-xs font-semibold uppercase" style="color: var(--theme-accent);">Manage &rarr;</a>
                </div>
                <p class="mt-2 font-[Bebas_Neue] text-4xl">{{ $catalogueTotal }} <span class="text-base font-normal" style="color: var(--theme-muted); font-family: 'Outfit', sans-serif;">titles</span></p>
                <div class="mt-3 flex flex-wrap gap-3 text-sm" style="color: var(--theme-muted);">
                    @foreach (['movie' => 'Movies', 'series' => 'Series', 'anime' => 'Anime', 'game' => 'Games'] as $type => $label)
                        <span>{{ $label }}: {{ $catalogueByType[$type] ?? 0 }}</span>
                    @endforeach
                </div>
                <p class="mt-3 text-sm" style="color: var(--theme-muted);">+{{ $catalogueAddedThisWeek }} added in the last 7 days</p>

                @if ($lastScheduledImport)
                    <p class="mt-2 text-xs" style="color: var(--theme-muted);">Last automatic import: {{ $lastScheduledImport['count'] }} title(s), {{ \Illuminate\Support\Carbon::parse($lastScheduledImport['at'])->diffForHumans() }}</p>
                @else
                    <p class="mt-2 text-xs" style="color: var(--theme-muted);">No automatic import has run yet — needs the scheduler (`schedule:work`) running to fire on its own.</p>
                @endif
            </div>

            <div class="rounded-[1.75rem] border p-6" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">News Library</h3>
                    <a href="{{ route('admin.news.index') }}" class="text-xs font-semibold uppercase" style="color: var(--theme-accent);">Manage &rarr;</a>
                </div>
                <p class="mt-2 font-[Bebas_Neue] text-4xl">{{ $newsTotal }} <span class="text-base font-normal" style="color: var(--theme-muted); font-family: 'Outfit', sans-serif;">articles</span></p>
                <p class="mt-3 text-sm" style="color: var(--theme-muted);">+{{ $newsAddedThisWeek }} added in the last 7 days</p>
                <p class="mt-2 text-xs" style="color: var(--theme-muted);">Grows via the "Import from RSS" button on the News page, or the daily schedule.</p>
            </div>
        </div>
    </div>
@endsection
