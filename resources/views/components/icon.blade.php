@props(['name', 'class' => 'h-5 w-5'])

@php
    $paths = [
        'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 3.5-1 5-2 7h16c-1-2-2-3.5-2-7Z"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 3v2M12 19v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M3 12h2M19 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/>',
        'moon' => '<path d="M20 14.5A8.5 8.5 0 1 1 9.5 4a7 7 0 0 0 10.5 10.5Z"/>',
        'palette' => '<path d="M12 3a9 9 0 1 0 0 18c1.1 0 2-.9 2-2 0-.5-.2-1-.5-1.3-.3-.4-.5-.8-.5-1.2 0-.9.7-1.5 1.5-1.5H16a4 4 0 0 0 4-4c0-4.4-3.6-8-8-8Z"/><circle cx="7.5" cy="11.5" r="1"/><circle cx="10.5" cy="7.5" r="1"/><circle cx="15" cy="8" r="1"/>',
        'heart' => '<path d="M12 20.5S3.5 15.4 3.5 9.7C3.5 6.8 5.8 4.5 8.6 4.5c1.5 0 2.9.7 3.4 1.9.5-1.2 1.9-1.9 3.4-1.9 2.8 0 5.1 2.3 5.1 5.2 0 5.7-8.5 10.8-8.5 10.8Z"/>',
        'calendar' => '<rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
        'image' => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5-4 4-2-2-5 5"/>',
        'flame' => '<path d="M12 22c4 0 7-2.7 7-6.8 0-3.5-2.3-5.8-3.8-8.4-.4.9-1.2 1.9-2.3 1.9-1.7 0-2.4-2-1.9-4.7C8 5 5 8.7 5 12.9 5 17.5 8 22 12 22Z"/>',
        'admin' => '<path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6l7-3Z"/><path d="M9 12l2 2 4-4"/>',
        'crown' => '<path d="M4 18h16l1-9-5 3-4-6-4 6-5-3 1 9Z"/><path d="M4 18v2h16v-2"/>',
        'list' => '<path d="M8 6h13M8 12h13M8 18h13"/><path d="M3 6h.01M3 12h.01M3 18h.01"/>',
    ];

    $inner = $paths[$name] ?? $paths['image'];
@endphp

<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" {{ $attributes->merge(['class' => $class, 'aria-hidden' => 'true']) }}>{!! $inner !!}</svg>
