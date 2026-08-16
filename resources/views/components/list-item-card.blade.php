@props([
    'item',
    'isFavourited' => false,
    'statusOptions' => null,
    'currentStatus' => null,
    'statusRoute' => null,
    'removeRoute' => null,
    'removeLabel' => 'Take off list',
])

<div class="overflow-hidden rounded-tl-[1.5rem] rounded-tr-[1.5rem] rounded-br-[1.5rem] rounded-bl-md border transition-all duration-300" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
    <a href="{{ route('titles.show', $item) }}" class="group block">
        <x-cover-image :src="$item->cover_image" :alt="$item->title" class="h-48 w-full object-cover" />
        <div class="p-4 pb-2">
            <span class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-accent);">{{ ucfirst($item->type) }}</span>
            <h3 class="mt-1 font-semibold transition-all duration-300 group-hover:text-[color:var(--theme-accent)]">{{ $item->title }}</h3>
        </div>
    </a>

    <div class="space-y-2 px-4 pb-4">
        <form method="POST" action="{{ route($isFavourited ? 'titles.favourite.destroy' : 'titles.favourite.store', $item) }}">
            @csrf
            @if ($isFavourited) @method('DELETE') @endif
            <button type="submit" class="w-full rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.04em] transition-all duration-300" style="border-color: var(--theme-accent); color: {{ $isFavourited ? 'var(--theme-bg)' : 'var(--theme-accent)' }}; background: {{ $isFavourited ? 'var(--theme-accent)' : 'transparent' }};">
                {{ $isFavourited ? '★ Favourited' : '☆ Add to Favourites' }}
            </button>
        </form>

        @if ($statusOptions && $statusRoute)
            <form method="POST" action="{{ route($statusRoute, $item) }}">
                @csrf
                <label class="sr-only" for="status-{{ $item->id }}">Change list status</label>
                <select id="status-{{ $item->id }}" name="status" onchange="this.form.submit()" class="w-full rounded-xl border px-3 py-1.5 text-xs" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ $currentStatus === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        @endif

        @if ($removeRoute)
            <form method="POST" action="{{ route($removeRoute, $item) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.04em] transition-all duration-300" style="border-color: var(--theme-border); color: var(--theme-muted);">
                    {{ $removeLabel }}
                </button>
            </form>
            <p class="text-center text-[10px] leading-snug" style="color: var(--theme-muted);">Only takes it off this list &mdash; it stays in the catalogue and you can add it back anytime.</p>
        @endif
    </div>
</div>
