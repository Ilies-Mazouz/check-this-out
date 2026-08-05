<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex h-11 items-center rounded-tl-xl rounded-tr-xl rounded-br-xl rounded-bl-md px-5 text-sm font-semibold transition-all duration-300 hover:-rotate-1 hover:scale-[1.02] focus:outline-none focus:ring-2']) }} style="background: var(--theme-accent); color: var(--theme-bg);">
    {{ $slot }}
</button>
