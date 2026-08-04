@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] border p-8 sm:p-10" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Dashboard</p>
            <h1 class="mt-4 font-[Bebas_Neue] text-5xl uppercase tracking-[0.18em]">Welcome back, {{ auth()->user()->username }}!</h1>
            <p class="mt-4 max-w-3xl text-lg leading-8" style="color: var(--theme-muted);">Track what you are watching, playing, and discovering across Check This Out.</p>
        </div>

        <div class="mt-8 grid gap-6 sm:grid-cols-2 md:grid-cols-4">
            @foreach ([
                ['label' => 'My Watchlist', 'route' => route('watchlist.mine')],
                ['label' => 'My Gaming List', 'route' => route('gaming.mine')],
                ['label' => 'My Favourites', 'route' => route('favourites.mine')],
                ['label' => 'Notifications', 'route' => route('notifications.index')],
            ] as $card)
                <a href="{{ $card['route'] }}" class="block rounded-[1.75rem] border p-6 transition-all duration-300 hover:-translate-y-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                    <h2 class="text-xl font-semibold">{{ $card['label'] }}</h2>
                    <p class="mt-2 text-sm" style="color: var(--theme-muted);">View &rarr;</p>
                </a>
            @endforeach
        </div>
    </div>
@endsection
