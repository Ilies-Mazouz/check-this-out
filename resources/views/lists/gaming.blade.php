@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">My Lists</p>
        <h1 class="mt-2 font-bold font-[Fredoka] text-5xl">Gaming List</h1>

        @foreach (['backlog' => 'Backlog', 'playing' => 'Playing', 'completed' => 'Completed', 'dropped' => 'Dropped', '100percent' => '100%'] as $status => $label)
            <div class="mt-10">
                <h2 class="text-xl font-semibold" style="color: var(--theme-accent);">{{ $label }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse ($entries->get($status, collect()) as $entry)
                        <a href="{{ route('titles.show', $entry->title) }}" class="rounded-xl border p-4 transition-all duration-300 hover:-translate-y-1" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border);">
                            <p class="font-semibold">{{ $entry->title->title }}</p>
                            <p class="mt-1 text-xs" style="color: var(--theme-muted);">Game</p>
                        </a>
                    @empty
                        <p class="text-sm" style="color: var(--theme-muted);">Nothing here yet.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
@endsection
