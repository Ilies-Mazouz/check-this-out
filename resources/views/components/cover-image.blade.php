@props(['src' => null, 'alt' => ''])

@php
    $extraStyle = $attributes->get('style');
@endphp

@if ($src)
    <img src="{{ asset('storage/'.$src) }}" alt="{{ $alt }}" {{ $attributes->except('style') }} @if ($extraStyle) style="{{ $extraStyle }}" @endif />
@else
    <div {{ $attributes->except('style')->merge(['class' => 'flex items-center justify-center']) }} style="background: linear-gradient(135deg, color-mix(in srgb, var(--theme-accent) 20%, transparent), color-mix(in srgb, var(--theme-accent-soft) 14%, transparent)), var(--theme-surface);{{ $extraStyle ? ' '.$extraStyle : '' }}">
        <x-icon name="image" class="h-10 w-10" style="opacity: 0.6; color: var(--theme-accent);" />
    </div>
@endif
