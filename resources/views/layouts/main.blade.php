<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Check This Out') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Bebas+Neue&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                color-scheme: {{ $theme['mode'] }};
            }

            body {
                background: var(--theme-bg);
                color: var(--theme-text);
                font-family: 'Outfit', sans-serif;
            }

            .theme-shell {
                background:
                    radial-gradient(circle at top left, color-mix(in srgb, var(--theme-accent) 16%, transparent), transparent 35%),
                    radial-gradient(circle at top right, color-mix(in srgb, var(--theme-accent-soft) 14%, transparent), transparent 34%),
                    var(--theme-bg);
            }

            .theme-surface {
                background: color-mix(in srgb, var(--theme-surface) 92%, transparent);
                border: 1px solid var(--theme-border);
                box-shadow: 0 18px 40px rgba(0, 0, 0, 0.18), var(--theme-glow);
            }

            .nav-link {
                position: relative;
                padding-bottom: 0.35rem;
                transition: all 300ms ease;
            }

            .nav-link::after {
                content: '';
                position: absolute;
                left: 0;
                bottom: 0;
                width: 100%;
                height: 2px;
                border-radius: 999px;
                background: var(--theme-accent);
                transform: scaleX(0);
                transform-origin: left;
                transition: transform 300ms ease;
            }

            .nav-link:hover {
                color: var(--theme-accent);
                text-shadow: 0 0 12px color-mix(in srgb, var(--theme-accent) 45%, transparent);
            }

            .nav-link:hover::after {
                transform: scaleX(1);
            }

            .logo-glow {
                text-shadow: 0 0 12px color-mix(in srgb, var(--theme-accent) 55%, transparent);
            }

            [data-theme-menu] {
                backdrop-filter: blur(18px);
            }
        </style>
    </head>
    <body style="{{ $theme['style'] }}">
        @php
            $themeChoices = [
                ['key' => 'neon_arcade', 'label' => 'Neon Arcade', 'swatch' => '#00f0ff'],
                ['key' => 'ember', 'label' => 'Ember', 'swatch' => '#ff6a00'],
                ['key' => 'spotlight', 'label' => 'Spotlight', 'swatch' => '#7c3aed'],
                ['key' => 'matinee', 'label' => 'Matinee', 'swatch' => '#e85400'],
            ];
        @endphp

        <div class="theme-shell min-h-screen flex flex-col">
            <header class="sticky top-0 z-50 h-16 border-b" style="background: {{ $theme['key'] === 'neon_arcade' ? 'rgba(8, 11, 20, 0.85)' : ($theme['key'] === 'ember' ? 'rgba(13, 8, 0, 0.85)' : ($theme['key'] === 'spotlight' ? 'rgba(245, 240, 255, 0.90)' : 'rgba(255, 248, 240, 0.90)')) }}; border-color: color-mix(in srgb, var(--theme-accent) 20%, transparent); backdrop-filter: blur(12px);">
                <div class="mx-auto flex h-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="shrink-0 font-['Bebas_Neue'] text-[1.8rem] leading-none tracking-[0.28em] text-[color:var(--theme-accent)] logo-glow">
                        Check This Out
                    </a>

                    <nav class="hidden flex-1 items-center justify-center gap-8 lg:flex">
                        <a class="nav-link text-sm font-medium uppercase tracking-[0.05em] text-[color:var(--theme-text)]/90" href="{{ route('catalogue') }}">Catalogue</a>
                        <a class="nav-link text-sm font-medium uppercase tracking-[0.05em] text-[color:var(--theme-text)]/90" href="{{ route('news.index') }}">News</a>
                        <a class="nav-link text-sm font-medium uppercase tracking-[0.05em] text-[color:var(--theme-text)]/90" href="{{ route('faq.index') }}">FAQ</a>
                        <a class="nav-link text-sm font-medium uppercase tracking-[0.05em] text-[color:var(--theme-text)]/90" href="{{ route('contact') }}">Contact</a>
                    </nav>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="relative" data-theme-switcher>
                            <button type="button" data-theme-toggle class="inline-flex h-10 w-10 items-center justify-center rounded-xl border text-[color:var(--theme-accent)] transition-all duration-300 hover:scale-105 hover:bg-[color:var(--theme-accent)] hover:text-[#050814]" style="border-color: color-mix(in srgb, var(--theme-accent) 70%, transparent); background: color-mix(in srgb, var(--theme-surface) 88%, transparent);" aria-label="Open theme switcher">
                                <span class="text-lg">🎨</span>
                            </button>

                            <div data-theme-menu class="absolute right-0 z-50 mt-3 hidden w-72 rounded-2xl p-3 theme-surface">
                                <p class="px-2 pb-3 text-xs font-semibold uppercase tracking-[0.12em] text-[color:var(--theme-muted)]">Themes</p>
                                <div class="space-y-2">
                                    @foreach ($themeChoices as $choice)
                                        <form method="POST" action="{{ route('theme.update') }}" class="m-0">
                                            @csrf
                                            <input type="hidden" name="theme" value="{{ $choice['key'] }}">
                                            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-left transition-all duration-300 hover:bg-[color:var(--theme-accent)]/10">
                                                <span class="flex h-5 w-5 items-center justify-center rounded-full border-2 transition-all duration-300 {{ $theme['key'] === $choice['key'] ? 'ring-2 ring-white ring-offset-2 ring-offset-transparent' : '' }}" style="background: {{ $choice['swatch'] }}; border-color: rgba(255,255,255,0.55); box-shadow: {{ $theme['key'] === $choice['key'] ? '0 0 14px rgba(255,255,255,0.95)' : 'none' }};"></span>
                                                <span class="text-sm font-semibold text-[color:var(--theme-text)]">{{ $choice['label'] }}</span>
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @guest
                            <a href="{{ route('login') }}" class="inline-flex h-10 items-center rounded-xl border px-4 text-sm font-semibold uppercase tracking-[0.04em] text-[color:var(--theme-accent)] transition-all duration-300 hover:bg-[color:var(--theme-accent)] hover:text-[#050814]" style="border-color: color-mix(in srgb, var(--theme-accent) 70%, transparent);">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex h-10 items-center rounded-xl border px-4 text-sm font-semibold uppercase tracking-[0.04em] text-[color:var(--theme-accent)] transition-all duration-300 hover:bg-[color:var(--theme-accent)] hover:text-[#050814]" style="border-color: color-mix(in srgb, var(--theme-accent) 70%, transparent);">
                                Register
                            </a>
                        @else
                            <div class="hidden items-center gap-3 md:flex">
                                <div class="flex items-center gap-3 rounded-2xl px-3 py-1.5 theme-surface">
                                    @if (auth()->user()->avatar)
                                        <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="{{ auth()->user()->username }}" class="h-8 w-8 rounded-full object-cover" />
                                    @else
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[color:var(--theme-accent)] text-xs font-bold text-[#050814]">
                                            {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="text-sm font-semibold text-[color:var(--theme-text)]">{{ auth()->user()->username }}</span>
                                </div>

                                <a href="{{ route('notifications.index') }}" class="text-sm font-semibold uppercase tracking-[0.04em] text-[color:var(--theme-text)]/90 transition-all duration-300 hover:text-[color:var(--theme-accent)]">
                                    Notifications
                                    @php $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count(); @endphp
                                    @if ($unreadCount > 0)
                                        <span class="ml-1 inline-flex h-5 w-5 items-center justify-center rounded-full text-xs" style="background: var(--theme-accent); color: var(--theme-bg);">{{ $unreadCount }}</span>
                                    @endif
                                </a>

                                <a href="{{ route('profile.edit') }}" class="text-sm font-semibold uppercase tracking-[0.04em] text-[color:var(--theme-text)]/90 transition-all duration-300 hover:text-[color:var(--theme-accent)]">Profile</a>

                                @if (auth()->user()->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold uppercase tracking-[0.04em] text-[color:var(--theme-text)]/90 transition-all duration-300 hover:text-[color:var(--theme-accent)]">Admin Panel</a>
                                @endif

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-sm font-semibold uppercase tracking-[0.04em] text-[color:var(--theme-text)]/90 transition-all duration-300 hover:text-[color:var(--theme-accent)]">Logout</button>
                                </form>
                            </div>
                        @endguest
                    </div>
                </div>
            </header>

            <main class="flex-1">
                @yield('content')
            </main>

            <footer class="border-t px-4 py-6" style="background: var(--theme-bg); border-color: color-mix(in srgb, var(--theme-accent) 20%, transparent);">
                <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 text-center text-sm text-[color:var(--theme-muted)] sm:flex-row sm:text-left">
                    <p>© 2026 Check This Out — Your Entertainment Hub</p>
                    <div class="flex items-center gap-4">
                        <a href="#" class="transition-all duration-300 hover:text-[color:var(--theme-accent)]">Privacy</a>
                        <a href="#" class="transition-all duration-300 hover:text-[color:var(--theme-accent)]">Terms</a>
                    </div>
                </div>
            </footer>
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

                    const toggleMenu = (event) => {
                        event.stopPropagation();
                        menu.classList.toggle('hidden');
                    };

                    toggle.addEventListener('click', toggleMenu);

                    menu.addEventListener('click', (event) => {
                        event.stopPropagation();
                    });

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