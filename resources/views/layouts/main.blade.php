<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Check This Out') }}</title>

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
        <div class="theme-shell min-h-screen flex flex-col">
            <header class="sticky top-0 z-50 h-16 border-b" style="background: var(--theme-navbar); border-color: color-mix(in srgb, var(--theme-accent) 20%, transparent); backdrop-filter: blur(12px);">
                <div class="mx-auto flex h-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="shrink-0 font-bold font-['Fredoka'] text-[1.8rem] leading-none text-[color:var(--theme-accent)] logo-glow">
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

                        @guest
                            <a href="{{ route('login') }}" class="inline-flex h-10 items-center rounded-xl border px-4 text-sm font-semibold uppercase tracking-[0.04em] text-[color:var(--theme-accent)] transition-all duration-300 hover:bg-[color:var(--theme-accent)] hover:text-[#050814]" style="border-color: color-mix(in srgb, var(--theme-accent) 70%, transparent);">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex h-10 items-center rounded-xl border px-4 text-sm font-semibold uppercase tracking-[0.04em] text-[color:var(--theme-accent)] transition-all duration-300 hover:bg-[color:var(--theme-accent)] hover:text-[#050814]" style="border-color: color-mix(in srgb, var(--theme-accent) 70%, transparent);">
                                Register
                            </a>
                        @else
                            <div class="hidden items-center gap-3 md:flex">
                                <a href="{{ route('lists.index') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl transition-all duration-300 hover:bg-[color:var(--theme-accent)]/10" aria-label="My Lists">
                                    <x-icon name="list" class="h-5 w-5" />
                                </a>

                                <a href="{{ route('notifications.index') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl transition-all duration-300 hover:bg-[color:var(--theme-accent)]/10" aria-label="Notifications">
                                    <x-icon name="bell" class="h-5 w-5" />
                                    @php $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count(); @endphp
                                    @if ($unreadCount > 0)
                                        <span class="absolute -right-0.5 -top-0.5 inline-flex h-4 min-w-4 items-center justify-center rounded-full px-1 text-[10px] font-bold" style="background: var(--theme-accent); color: var(--theme-bg);">{{ $unreadCount }}</span>
                                    @endif
                                </a>

                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-2xl px-3 py-1.5 theme-surface transition-all duration-300 hover:opacity-80">
                                    @if (auth()->user()->avatar)
                                        <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="{{ auth()->user()->username }}" class="h-8 w-8 rounded-full object-cover" />
                                    @else
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[color:var(--theme-accent)] text-xs font-bold text-[#050814]">
                                            {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="text-sm font-semibold text-[color:var(--theme-text)]">{{ auth()->user()->username }}</span>
                                </a>

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
                <div class="mx-auto mt-4 flex max-w-7xl flex-col items-center gap-2 text-center text-xs text-[color:var(--theme-muted)] sm:flex-row sm:text-left">
                    <a href="https://www.themoviedb.org/" target="_blank" rel="noopener noreferrer" class="shrink-0 opacity-70 transition-opacity duration-300 hover:opacity-100">
                        <img src="{{ asset('images/tmdb-logo.svg') }}" alt="TMDB" class="h-4 w-auto" />
                    </a>
                    <p>This product uses the TMDB API but is not endorsed, certified, or otherwise approved by TMDB. Anime data from <a href="https://anilist.co/" target="_blank" rel="noopener noreferrer" class="underline hover:no-underline">AniList</a>. Game data from <a href="https://www.igdb.com/" target="_blank" rel="noopener noreferrer" class="underline hover:no-underline">IGDB</a>. News aggregated from their original publishers, linked on each article.</p>
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