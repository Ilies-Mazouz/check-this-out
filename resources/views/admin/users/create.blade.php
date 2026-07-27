@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-xl space-y-6">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin</p>
            <h2 class="mt-2 font-[Bebas_Neue] text-4xl uppercase tracking-[0.18em]">New User</h2>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5 rounded-[1.75rem] border p-6" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            @csrf

            <div>
                <x-input-label for="username" value="Username" />
                <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username')" required minlength="2" autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('username')" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required minlength="8" />
                <x-input-error class="mt-2" :messages="$errors->get('password')" />
            </div>

            <label class="flex items-center gap-3 text-sm font-medium">
                <input type="checkbox" name="is_admin" value="1" class="rounded" {{ old('is_admin') ? 'checked' : '' }} />
                Make this user an admin
            </label>

            <div class="flex items-center gap-4">
                <x-primary-button>Create user</x-primary-button>
                <a href="{{ route('admin.users.index') }}" class="text-sm" style="color: var(--theme-muted);">Cancel</a>
            </div>
        </form>
    </div>
@endsection
