@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] border p-8 sm:p-10" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <div class="flex flex-col items-center gap-6 text-center sm:flex-row sm:text-left">
                @if ($profileUser->avatar)
                    <img src="{{ asset('storage/'.$profileUser->avatar) }}" alt="{{ $profileUser->username }}" class="h-28 w-28 rounded-2xl object-cover" style="box-shadow: var(--theme-glow);" />
                @else
                    <div class="flex h-28 w-28 items-center justify-center rounded-2xl text-4xl font-bold" style="background: var(--theme-accent); color: var(--theme-bg);">
                        {{ strtoupper(substr($profileUser->username, 0, 1)) }}
                    </div>
                @endif

                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Profile</p>
                    <h1 class="mt-2 font-[Bebas_Neue] text-5xl uppercase tracking-[0.18em]">{{ $profileUser->username }}</h1>
                    @if ($profileUser->birthday)
                        <p class="mt-2 text-sm" style="color: var(--theme-muted);">🎂 {{ $profileUser->birthday->format('F j, Y') }}</p>
                    @endif
                    <p class="mt-1 text-sm" style="color: var(--theme-muted);">Member since {{ $profileUser->created_at->format('F Y') }}</p>
                </div>

                @auth
                    @if (auth()->id() !== $profileUser->id)
                        <form method="POST" action="{{ route('users.block', $profileUser) }}" class="sm:ml-auto">
                            @csrf
                            @if (auth()->user()->hasBlocked($profileUser))
                                @method('DELETE')
                                <button type="submit" class="inline-flex h-10 items-center rounded-xl border px-4 text-sm font-semibold uppercase tracking-[0.04em] transition-all duration-300" style="border-color: var(--theme-border); color: var(--theme-muted);">Unblock</button>
                            @else
                                <button type="submit" class="inline-flex h-10 items-center rounded-xl border px-4 text-sm font-semibold uppercase tracking-[0.04em] transition-all duration-300" style="border-color: var(--theme-border); color: var(--theme-muted);">Block user</button>
                            @endif
                        </form>
                    @endif
                @endauth
            </div>

            @if ($profileUser->bio)
                <div class="mt-8 border-t pt-6" style="border-color: var(--theme-border);">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em]" style="color: var(--theme-muted);">About</p>
                    <p class="mt-3 max-w-2xl text-lg leading-8">{{ $profileUser->bio }}</p>
                </div>
            @endif
        </div>

        <div class="mt-8 grid gap-6 sm:grid-cols-2">
            <div class="rounded-[1.75rem] border p-6" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                <h2 class="text-xl font-semibold">Reviews</h2>
                <p class="mt-2 text-sm" style="color: var(--theme-muted);">{{ $profileUser->reviews()->count() }} review(s) written</p>
            </div>
            <div class="rounded-[1.75rem] border p-6" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                <h2 class="text-xl font-semibold">Favourites</h2>
                <p class="mt-2 text-sm" style="color: var(--theme-muted);">{{ $profileUser->favourites()->count() }} title(s) favourited</p>
            </div>
        </div>
    </div>
@endsection
