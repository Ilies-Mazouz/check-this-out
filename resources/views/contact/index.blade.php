@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Get in touch</p>
        <h1 class="mt-2 font-bold font-[Fredoka] text-5xl">Contact</h1>
        <p class="mt-4 text-lg" style="color: var(--theme-muted);">Questions, feedback, or a title you want added? Send us a message.</p>

        @auth
            <a href="{{ route('contact.messages.index') }}" class="mt-3 inline-block text-sm font-semibold underline" style="color: var(--theme-accent);">View your past messages &amp; answers &rarr;</a>
        @endauth

        @if (session('status') === 'contact-sent')
            <p class="mt-6 rounded-xl border px-4 py-3 text-sm" style="border-color: var(--theme-accent); color: var(--theme-accent);">Thanks! Your message has been sent.</p>
        @endif

        <form method="POST" action="{{ route('contact.store') }}" class="mt-8 space-y-5 rounded-tl-[1.75rem] rounded-tr-[1.75rem] rounded-br-[1.75rem] rounded-bl-md border p-6 sm:p-8" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            @csrf

            <div>
                <x-input-label for="name" value="Name" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', auth()->user()->username ?? '')" required minlength="2" autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', auth()->user()->email ?? '')" required />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="subject" value="Subject" />
                <x-text-input id="subject" name="subject" type="text" class="mt-1 block w-full" :value="old('subject')" required minlength="3" />
                <x-input-error class="mt-2" :messages="$errors->get('subject')" />
            </div>

            <div>
                <x-input-label for="body" value="Message" />
                <textarea id="body" name="body" rows="6" maxlength="5000" class="mt-1 block w-full rounded-xl border px-4 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);" required minlength="10">{{ old('body') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('body')" />
            </div>

            <x-primary-button>Send message</x-primary-button>
        </form>
    </div>
@endsection
