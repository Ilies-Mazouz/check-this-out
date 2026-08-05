<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Check This Out') }} - Admin Panel</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Fredoka:wght@500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                color-scheme: {{ $theme['mode'] }};
            }

            body {
                background: var(--theme-bg);
                color: var(--theme-text);
                font-family: 'Space Grotesk', sans-serif;
            }

            .admin-shell {
                background:
                    radial-gradient(circle at top left, color-mix(in srgb, var(--theme-accent) 14%, transparent), transparent 30%),
                    var(--theme-bg);
            }

            .theme-surface {
                background: color-mix(in srgb, var(--theme-surface) 92%, transparent);
                border: 1px solid var(--theme-border);
                box-shadow: 0 18px 40px rgba(0, 0, 0, 0.18), var(--theme-glow);
            }

            .sidebar-link {
                transition: all 300ms ease;
            }

            .sidebar-link:hover {
                transform: translateX(2px);
                background: color-mix(in srgb, var(--theme-accent) 18%, transparent);
                border-color: color-mix(in srgb, var(--theme-accent) 45%, transparent);
            }
        </style>
    </head>
    <body style="{{ $theme['style'] }}">
        @php
            $sidebarLinks = [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 13.5V20h6v-6.5H4Zm10 0V20h6v-10h-6v3.5ZM4 4v7h6V4H4Zm10 0v7h6V4h-6Z"/></svg>'],
                ['label' => 'News', 'route' => 'admin.news.index', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5h14v14H5z"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>'],
                ['label' => 'FAQ', 'route' => 'admin.faq.index', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 17h.01"/><path d="M12 13.5c1.8 0 3-1.1 3-2.8 0-1.7-1.3-2.7-3-2.7-1.6 0-2.9 1-3 2.6"/><circle cx="12" cy="12" r="9"/></svg>'],
                ['label' => 'Titles', 'route' => 'admin.titles.index', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 6h14v12H5z"/><path d="M8 10h8M8 14h5"/></svg>'],
                ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 19a4 4 0 0 0-8 0"/><circle cx="12" cy="8" r="3.5"/></svg>'],
                ['label' => 'Messages', 'route' => 'admin.contact-messages.index', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16v11H7l-3 3z"/><path d="M7 9h10M7 12h6"/></svg>'],
            ];
        @endphp

        <div class="admin-shell min-h-screen">
            <div class="flex min-h-screen flex-col lg:flex-row">
                <aside class="hidden w-[260px] shrink-0 flex-col border-r p-5 lg:flex" style="background: color-mix(in srgb, var(--theme-navbar) 96%, transparent); border-color: color-mix(in srgb, var(--theme-accent) 24%, transparent);">
                    <a href="{{ route('dashboard') }}" class="mb-8 flex items-center gap-2 text-[1.5rem] font-bold text-[color:var(--theme-accent)]" style="font-family: 'Fredoka', sans-serif; text-shadow: 0 0 12px color-mix(in srgb, var(--theme-accent) 55%, transparent);">
                        <x-icon name="admin" class="h-6 w-6" /> Admin Panel
                    </a>

                    <nav class="space-y-2 text-sm font-medium">
                        @foreach ($sidebarLinks as $link)
                            <a href="{{ route($link['route']) }}" class="sidebar-link flex items-center gap-3 rounded-2xl border px-4 py-3 text-[color:var(--theme-text)]/90" style="border-color: color-mix(in srgb, var(--theme-border) 75%, transparent);">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-[color:var(--theme-border)] text-[color:var(--theme-accent)]">{!! $link['icon'] !!}</span>
                                <span>{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>
                </aside>

                <div class="flex min-w-0 flex-1 flex-col">
                    <header class="sticky top-0 z-40 h-16 border-b" style="background: var(--theme-navbar); border-color: color-mix(in srgb, var(--theme-accent) 20%, transparent); backdrop-filter: blur(12px);">
                        <div class="mx-auto flex h-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[color:var(--theme-muted)]">Admin Panel</p>
                                <h1 class="font-bold font-['Fredoka'] text-[1.8rem] leading-none">Admin Panel</h1>
                            </div>

                            <div class="flex items-center gap-2 sm:gap-3">
                                <div class="relative" data-theme-switcher>
                                    <button type="button" data-theme-toggle class="inline-flex h-10 w-10 items-center justify-center rounded-xl border text-[color:var(--theme-accent)] transition-all duration-300 hover:scale-105 hover:bg-[color:var(--theme-accent)] hover:text-[#050814]" style="border-color: color-mix(in srgb, var(--theme-accent) 70%, transparent); background: color-mix(in srgb, var(--theme-surface) 88%, transparent);" aria-label="Open theme switcher">
                                        <x-icon name="palette" class="h-5 w-5" />
                                    </button>

                                    <div data-theme-menu class="absolute right-0 z-50 mt-3 hidden w-72 rounded-2xl p-3 theme-surface">
                                        <p class="px-2 pb-2 text-xs font-semibold uppercase tracking-[0.12em] text-[color:var(--theme-muted)]">Appearance</p>
                                        <div class="flex gap-2 pb-3">
                                            @foreach (['dark' => 'moon', 'light' => 'sun'] as $mode => $iconName)
                                                <form method="POST" action="{{ route('theme.update') }}" class="flex-1">
                                                    @csrf
                                                    <input type="hidden" name="theme" value="{{ $theme['family'] }}_{{ $mode }}">
                                                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border py-2 text-sm font-semibold transition-all duration-300" style="border-color: {{ $theme['mode'] === $mode ? 'var(--theme-accent)' : 'var(--theme-border)' }}; color: {{ $theme['mode'] === $mode ? 'var(--theme-accent)' : 'var(--theme-text)' }};">
                                                        <x-icon :name="$iconName" class="h-4 w-4" /> {{ ucfirst($mode) }}
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>

                                        <p class="px-0 pb-2 text-xs font-semibold uppercase tracking-[0.12em] text-[color:var(--theme-muted)]">Theme</p>
                                        <div class="space-y-2">
                                            @foreach ($themeFamilies as $familyKey => $family)
                                                <form method="POST" action="{{ route('theme.update') }}" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="theme" value="{{ $familyKey }}_{{ $theme['mode'] }}">
                                                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-left transition-all duration-300 hover:bg-[color:var(--theme-accent)]/10">
                                                        <span class="flex h-5 w-5 items-center justify-center rounded-full border-2 transition-all duration-300 {{ $theme['family'] === $familyKey ? 'ring-2 ring-white ring-offset-2 ring-offset-transparent' : '' }}" style="background: {{ $family['swatch'] }}; border-color: rgba(255,255,255,0.55); box-shadow: {{ $theme['family'] === $familyKey ? '0 0 14px rgba(255,255,255,0.95)' : 'none' }};"></span>
                                                        <span class="text-sm font-semibold text-[color:var(--theme-text)]">{{ $family['label'] }}</span>
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <span class="hidden rounded-xl border px-3 py-2 text-sm font-semibold sm:inline-flex" style="border-color: color-mix(in srgb, var(--theme-border) 80%, transparent); background: color-mix(in srgb, var(--theme-surface) 88%, transparent);">{{ auth()->user()->username }}</span>
                                <a href="{{ route('dashboard') }}" class="inline-flex h-10 items-center rounded-xl border px-4 text-sm font-semibold uppercase tracking-[0.04em] text-[color:var(--theme-accent)] transition-all duration-300 hover:bg-[color:var(--theme-accent)] hover:text-[#050814]" style="border-color: color-mix(in srgb, var(--theme-accent) 70%, transparent);">
                                    Main Site
                                </a>
                            </div>
                        </div>
                    </header>

                    <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                        @yield('content')
                    </main>

                    <footer class="border-t px-4 py-5" style="background: var(--theme-bg); border-color: color-mix(in srgb, var(--theme-accent) 20%, transparent);">
                        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 text-center text-sm text-[color:var(--theme-muted)] sm:flex-row sm:text-left">
                            <p>© 2026 Check This Out — Your Entertainment Hub</p>
                            <div class="flex items-center gap-4">
                                <a href="#" class="transition-all duration-300 hover:text-[color:var(--theme-accent)]">Privacy</a>
                                <a href="#" class="transition-all duration-300 hover:text-[color:var(--theme-accent)]">Terms</a>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>

        <script>
            (() => {
                const switchers = document.querySelectorAll('[data-theme-switcher]');

                switchers.forEach((switcher) => {
                    const toggle = switcher.querySelector('[data-theme-toggle]');
                    const menu = switcher.querySelector('[data-theme-menu]');

                    if (!toggle || !menu) {
                        return;
                    }

                    const closeMenu = () => {
                        menu.classList.add('hidden');
                    };

                    toggle.addEventListener('click', (event) => {
                        event.stopPropagation();
                        menu.classList.toggle('hidden');
                    });

                    menu.addEventListener('click', (event) => event.stopPropagation());
                    document.addEventListener('click', closeMenu);
                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            closeMenu();
                        }
                    });
                });
            })();
        </script>
    </body>
</html>