@extends('layouts.main')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] border p-6 sm:p-8" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="rounded-[2rem] border p-6 sm:p-8" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="rounded-[2rem] border p-6 sm:p-8" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <div class="max-w-2xl">
                <h2 class="text-lg font-medium">Notification Preferences</h2>
                <form method="POST" action="{{ route('notification-settings.update') }}" class="mt-4 space-y-4">
                    @csrf
                    <label class="flex items-center gap-3 text-sm font-medium">
                        <input type="checkbox" name="notify_on_comment_reply" value="1" class="rounded" {{ ($user->notificationSetting->notify_on_comment_reply ?? true) ? 'checked' : '' }} />
                        Notify me when someone replies to my comments
                    </label>
                    <x-primary-button>Save preference</x-primary-button>
                </form>
            </div>
        </div>

        <div class="rounded-[2rem] border p-6 sm:p-8" style="background: color-mix(in srgb, var(--theme-surface) 94%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
