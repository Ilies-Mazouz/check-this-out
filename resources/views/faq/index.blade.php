@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Help</p>
        <h1 class="mt-2 font-bold font-[Fredoka] text-5xl">Frequently Asked Questions</h1>

        <div class="mt-10 space-y-10">
            @forelse ($categories as $category)
                <div>
                    <h2 class="text-2xl font-semibold" style="color: var(--theme-accent);">{{ $category->name }}</h2>
                    <div class="mt-4 space-y-3" x-data="{ open: null }">
                        @foreach ($category->faqItems as $item)
                            <div class="rounded-2xl border p-5" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border);">
                                <button type="button" class="flex w-full items-center justify-between text-left font-semibold" @click="open === {{ $item->id }} ? open = null : open = {{ $item->id }}">
                                    {{ $item->question }}
                                    <span>+</span>
                                </button>
                                <p class="mt-3 text-sm leading-7" style="color: var(--theme-muted);" x-show="open === {{ $item->id }}" x-transition>{{ $item->answer }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p style="color: var(--theme-muted);">No FAQ entries yet.</p>
            @endforelse
        </div>

        <div class="mt-12 border-t pt-8" style="border-color: var(--theme-border);">
            <h2 class="text-xl font-semibold">Don't see your question?</h2>

            @auth
                @if (session('status') === 'faq-suggestion-sent')
                    <p class="mt-3 text-sm" style="color: var(--theme-accent);">Thanks — an admin will take a look.</p>
                @endif
                <form method="POST" action="{{ route('faq.suggestions.store') }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
                    @csrf
                    <input type="text" name="question" required minlength="5" maxlength="500" placeholder="Suggest a question..." class="flex-1 rounded-xl border px-4 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);" />
                    <x-primary-button>Suggest</x-primary-button>
                </form>
            @else
                <p class="mt-3 text-sm" style="color: var(--theme-muted);"><a href="{{ route('login') }}" class="underline">Log in</a> to suggest a question for the FAQ.</p>
            @endauth
        </div>
    </div>
@endsection
