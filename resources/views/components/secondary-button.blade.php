<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex h-11 items-center rounded-tl-xl rounded-tr-xl rounded-br-xl rounded-bl-md border px-5 text-sm font-semibold transition-all duration-300 hover:bg-[color:var(--theme-accent)]/10 disabled:opacity-25']) }} style="border-color: var(--theme-border); color: var(--theme-text); background: color-mix(in srgb, var(--theme-surface) 92%, transparent);">
    {{ $slot }}
</button>
