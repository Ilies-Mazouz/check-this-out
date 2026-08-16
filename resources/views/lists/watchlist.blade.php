@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">My Lists</p>
        <h1 class="mt-2 font-bold font-[Fredoka] text-5xl">Watchlist</h1>

        @php
            $statusOptions = ['want_to_watch' => 'Want to Watch', 'watching' => 'Watching', 'completed' => 'Completed', 'dropped' => 'Dropped'];
        @endphp

        @foreach ($statusOptions as $status => $label)
            <div class="mt-10">
                <h2 class="text-xl font-semibold" style="color: var(--theme-accent);">{{ $label }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse ($entries->get($status, collect()) as $entry)
                        <x-list-item-card
                            :item="$entry->title"
                            :is-favourited="$favouriteIds->contains($entry->title_id)"
                            :status-options="$statusOptions"
                            :current-status="$entry->status"
                            status-route="titles.watchlist.update"
                            remove-route="titles.watchlist.destroy"
                            remove-label="Take off Watchlist"
                        />
                    @empty
                        <p class="text-sm" style="color: var(--theme-muted);">Nothing here yet.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
@endsection
