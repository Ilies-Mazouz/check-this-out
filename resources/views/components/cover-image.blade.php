@props(['src' => null, 'alt' => '', 'icon' => '🎬'])

@php
    $extraStyle = $attributes->get('style');
@endphp

@if ($src)
    <img src="{{ asset('storage/'.$src) }}" alt="{{ $alt }}" {{ $attributes->except('style') }} @if ($extraStyle) style="{{ $extraStyle }}" @endif />
@else
    <div {{ $attributes->except('style')->merge(['class' => 'flex items-center justify-center']) }} style="background: linear-gradient(135deg, color-mix(in srgb, var(--theme-accent) 20%, transparent), color-mix(in srgb, var(--theme-accent-soft) 14%, transparent)), var(--theme-surface);{{ $extraStyle ? ' '.$extraStyle : '' }}">
        <span class="text-4xl" style="opacity: 0.75; color: var(--theme-accent);">{{ $icon }}</span>
    </div>
@endif
