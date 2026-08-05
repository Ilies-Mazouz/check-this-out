@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <a href="{{ route('admin.news.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold uppercase tracking-[0.04em] transition-all duration-300 hover:text-[color:var(--theme-accent)]" style="color: var(--theme-muted);">&larr; Back to News</a>

        <div>
            <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin</p>
            <h2 class="mt-2 font-bold font-[Fredoka] text-4xl">New Article</h2>
        </div>

        <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data" class="space-y-5 rounded-tl-[1.75rem] rounded-tr-[1.75rem] rounded-br-[1.75rem] rounded-bl-md border p-6" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            @include('admin.news._form', ['article' => null])

            <div class="flex items-center gap-4">
                <x-primary-button>Create article</x-primary-button>
                <a href="{{ route('admin.news.index') }}" class="text-sm" style="color: var(--theme-muted);">Cancel</a>
            </div>
        </form>
    </div>
@endsection
