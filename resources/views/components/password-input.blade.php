@props(['disabled' => false])

<div class="relative" x-data="{ show: false }">
    <input :type="show ? 'text' : 'password'" @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-xl border px-4 py-2 pr-14 text-sm shadow-sm focus:outline-none focus:ring-2']) }} style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">
    <button type="button" @click="show = !show" tabindex="-1" class="absolute inset-y-0 right-0 flex items-center px-3 text-xs font-semibold uppercase tracking-[0.05em] transition-all duration-300" style="color: var(--theme-muted);">
        <span x-text="show ? 'Hide' : 'Show'"></span>
    </button>
</div>
