<section>
    <header>
        <h2 class="text-lg font-medium">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm" style="color: var(--theme-muted);">
            {{ __("Update your account's profile information, avatar, and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required autofocus autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2" style="color: var(--theme-text);">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" style="color: var(--theme-accent);">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="birthday" :value="__('Birthday')" />
            <x-text-input id="birthday" name="birthday" type="date" class="mt-1 block w-full" :value="old('birthday', optional($user->birthday)->format('Y-m-d'))" />
            <x-input-error class="mt-2" :messages="$errors->get('birthday')" />
        </div>

        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full rounded-xl border px-4 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div>
            <x-input-label for="avatar" :value="__('Avatar')" />
            <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full text-sm file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200" style="color: var(--theme-text);" />
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            <input type="hidden" id="remove-avatar-input" name="remove_avatar" value="0" />

            <div class="mt-3 flex items-center gap-4">
                <img id="avatar-preview" src="{{ $user->avatar ? asset('storage/'.$user->avatar) : '' }}" alt="{{ $user->username }} avatar" class="h-16 w-16 rounded-2xl object-cover {{ $user->avatar ? '' : 'hidden' }}" />
                <p id="avatar-preview-label" class="text-sm" style="color: var(--theme-muted);">{{ $user->avatar ? __('Current avatar') : '' }}</p>
                <button type="button" id="remove-avatar-btn" class="text-sm underline {{ $user->avatar ? '' : 'hidden' }}" style="color: var(--theme-muted);">{{ __('Remove avatar') }}</button>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm"
                    style="color: var(--theme-muted);"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

<script>
    (() => {
        const input = document.getElementById('avatar');
        const preview = document.getElementById('avatar-preview');
        const label = document.getElementById('avatar-preview-label');
        const removeInput = document.getElementById('remove-avatar-input');
        const removeBtn = document.getElementById('remove-avatar-btn');
        const hadAvatar = {{ $user->avatar ? 'true' : 'false' }};

        input.addEventListener('change', () => {
            const file = input.files[0];

            if (!file) {
                return;
            }

            removeInput.value = '0';
            removeBtn.classList.toggle('hidden', !hadAvatar);

            const reader = new FileReader();
            reader.onload = (event) => {
                preview.src = event.target.result;
                preview.classList.remove('hidden');
                label.textContent = 'New avatar (not saved yet)';
            };
            reader.readAsDataURL(file);
        });

        removeBtn.addEventListener('click', () => {
            input.value = '';
            removeInput.value = '1';
            preview.src = '';
            preview.classList.add('hidden');
            label.textContent = 'Avatar will be removed on save';
            removeBtn.classList.add('hidden');
        });
    })();
</script>
