@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium']) }} style="color: var(--theme-text);">
    {{ $value ?? $slot }}
</label>
