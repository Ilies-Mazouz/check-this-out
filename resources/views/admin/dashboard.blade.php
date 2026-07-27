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
                'Pending Titles',
                'News Articles',
                'Open Contact Messages',
                'Active Users',
            ] as $label)
                <div class="rounded-[1.75rem] border p-6" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em]" style="color: var(--theme-muted);">{{ $label }}</p>
                    <div class="mt-4 h-20 rounded-2xl border border-dashed" style="border-color: var(--theme-border);"></div>
                </div>
            @endforeach
        </div>
    </div>
@endsection