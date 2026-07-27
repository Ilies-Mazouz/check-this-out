@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin</p>
            <h2 class="mt-2 font-[Bebas_Neue] text-4xl uppercase tracking-[0.18em]">New Title</h2>
        </div>

        <form method="POST" action="{{ route('admin.titles.store') }}" enctype="multipart/form-data" class="space-y-5 rounded-[1.75rem] border p-6" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            @include('titles._form')

            <div class="flex items-center gap-4">
                <x-primary-button>Create &amp; publish</x-primary-button>
                <a href="{{ route('admin.titles.index') }}" class="text-sm" style="color: var(--theme-muted);">Cancel</a>
            </div>
        </form>
    </div>
@endsection
