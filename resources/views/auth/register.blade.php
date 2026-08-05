@extends('layouts.main')

@section('content')
    <div class="mx-auto flex max-w-md items-center px-4 py-16 sm:px-6 lg:px-8">
        <div class="w-full rounded-tl-[2rem] rounded-tr-[2rem] rounded-br-[2rem] rounded-bl-md border p-8 sm:p-10" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <div class="mb-8 text-center">
                <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Join the platform</p>
                <h1 class="mt-4 font-bold font-[Fredoka] text-5xl">Register</h1>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="username" :value="__('Username')" />
                    <x-text-input id="username" class="mt-1 block w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-password-input id="password" class="mt-1" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-password-input id="password_confirmation" class="mt-1" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between gap-4">
                    <a class="text-sm font-semibold underline underline-offset-4" href="{{ route('login') }}">{{ __('Already registered?') }}</a>
                    <x-primary-button>
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection
