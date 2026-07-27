@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin</p>
                <h2 class="mt-2 font-[Bebas_Neue] text-4xl uppercase tracking-[0.18em]">Manage News</h2>
            </div>
            <a href="{{ route('admin.news.create') }}" class="inline-flex h-11 items-center rounded-xl border px-5 text-sm font-semibold uppercase tracking-[0.04em]" style="border-color: var(--theme-accent); color: var(--theme-accent);">+ New Article</a>
        </div>

        @if (session('status'))
            <p class="rounded-xl border px-4 py-3 text-sm" style="border-color: var(--theme-border); color: var(--theme-muted);">Done.</p>
        @endif

        <div class="overflow-x-auto rounded-[1.75rem] border" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b text-xs uppercase tracking-[0.15em]" style="border-color: var(--theme-border); color: var(--theme-muted);">
                        <th class="px-5 py-4">Title</th>
                        <th class="px-5 py-4">Published</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articles as $article)
                        <tr class="border-b" style="border-color: color-mix(in srgb, var(--theme-border) 60%, transparent);">
                            <td class="px-5 py-4 font-semibold">{{ $article->title }}</td>
                            <td class="px-5 py-4" style="color: var(--theme-muted);">{{ $article->published_at?->format('M j, Y') ?? 'Draft' }}</td>
                            <td class="px-5 py-4 text-right space-x-2">
                                <a href="{{ route('admin.news.edit', $article) }}" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">Edit</a>
                                <form method="POST" action="{{ route('admin.news.destroy', $article) }}" class="inline" onsubmit="return confirm('Delete this article?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $articles->links() }}
    </div>
@endsection
