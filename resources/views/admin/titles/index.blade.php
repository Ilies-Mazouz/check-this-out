@extends('layouts.admin')

@section('content')
    <div class="space-y-6" x-data="{ rejecting: null }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin</p>
                <h2 class="mt-2 font-[Bebas_Neue] text-4xl uppercase tracking-[0.18em]">Manage Titles</h2>
            </div>
            <a href="{{ route('admin.titles.create') }}" class="inline-flex h-11 items-center rounded-xl border px-5 text-sm font-semibold uppercase tracking-[0.04em]" style="border-color: var(--theme-accent); color: var(--theme-accent);">+ New Title</a>
        </div>

        <div class="flex gap-2 text-sm">
            @foreach (['pending' => 'Pending', 'accepted' => 'Accepted', 'rejected' => 'Rejected', 'all' => 'All'] as $value => $label)
                <a href="{{ route('admin.titles.index', ['status' => $value]) }}" class="rounded-xl border px-3 py-1.5 font-semibold" style="border-color: {{ $status === $value ? 'var(--theme-accent)' : 'var(--theme-border)' }}; color: {{ $status === $value ? 'var(--theme-accent)' : 'var(--theme-muted)' }};">{{ $label }}</a>
            @endforeach
        </div>

        <div class="space-y-4">
            @forelse ($titles as $title)
                <div class="rounded-[1.5rem] border p-5" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-accent);">{{ ucfirst($title->type) }} · {{ ucfirst($title->status) }}</span>
                            <h3 class="mt-1 text-lg font-semibold">{{ $title->title }}</h3>
                            <p class="text-sm" style="color: var(--theme-muted);">
                                Submitted by {{ $title->submittedBy?->username ?? 'Admin' }}
                            </p>
                            @if ($title->status === 'rejected' && $title->rejection_reason)
                                <p class="mt-1 text-sm" style="color: var(--theme-muted);">Reason: {{ $title->rejection_reason }}</p>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @if ($title->status === 'accepted')
                                <a href="{{ route('titles.show', $title) }}" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">View</a>
                            @endif

                            @if ($title->status !== 'accepted')
                                <form method="POST" action="{{ route('admin.titles.approve', $title) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-accent); color: var(--theme-accent);">Approve</button>
                                </form>
                            @endif

                            @if ($title->status !== 'rejected')
                                <button type="button" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);" @click="rejecting === {{ $title->id }} ? rejecting = null : rejecting = {{ $title->id }}">Reject</button>
                            @endif

                            <form method="POST" action="{{ route('admin.titles.destroy', $title) }}" onsubmit="return confirm('Delete this title permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">Delete</button>
                            </form>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.titles.reject', $title) }}" class="mt-4" x-show="rejecting === {{ $title->id }}" x-transition>
                        @csrf
                        @method('POST')
                        <textarea name="rejection_reason" rows="2" required placeholder="Reason for rejection..." class="w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);"></textarea>
                        <button type="submit" class="mt-2 rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">Confirm rejection</button>
                    </form>
                </div>
            @empty
                <p style="color: var(--theme-muted);">Nothing here.</p>
            @endforelse
        </div>

        {{ $titles->links() }}
    </div>
@endsection
