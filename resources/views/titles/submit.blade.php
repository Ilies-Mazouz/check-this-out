@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Contribute</p>
        <h1 class="mt-2 font-bold font-[Fredoka] text-5xl">Submit a Title</h1>
        <p class="mt-4 text-lg" style="color: var(--theme-muted);">Your submission is reviewed by an admin before it appears in the catalogue. You'll be notified once it's approved or rejected.</p>

        <form method="POST" action="{{ route('titles.submit.store') }}" enctype="multipart/form-data" class="mt-8 space-y-5 rounded-tl-[1.75rem] rounded-tr-[1.75rem] rounded-br-[1.75rem] rounded-bl-md border p-6 sm:p-8" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            @include('titles._form')

            <x-primary-button>Submit for review</x-primary-button>
        </form>
    </div>
@endsection
