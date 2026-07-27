<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Check This Out') }} | Ember Cinema</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                background: #0F0D0C;
                color: #F5F2F0;
                font-family: 'Inter', sans-serif;
            }

            .headline {
                font-family: 'Playfair Display', serif;
            }

            .ember-shell {
                background:
                    radial-gradient(circle at top left, rgba(255, 107, 0, 0.12), transparent 28%),
                    radial-gradient(circle at top right, rgba(255, 168, 0, 0.08), transparent 22%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0.02), transparent 24%),
                    #0F0D0C;
            }

            .ember-ambient {
                box-shadow:
                    0 14px 32px rgba(255, 107, 0, 0.08),
                    0 0 0 1px rgba(255, 168, 0, 0.08);
            }
        </style>
    </head>
    <body class="bg-[#0F0D0C] text-[#F5F2F0] antialiased">
        @php
            $catalogueItems = [
                ['kind' => 'Movie', 'title' => 'Ember Tide', 'genre' => 'Drama / Mystery', 'background' => 'from-[#1A1615] via-[#2A1E1A] to-[#0F0D0C]'],
                ['kind' => 'Game', 'title' => 'Solar Rush', 'genre' => 'Racing / Arcade', 'background' => 'from-[#1A1615] via-[#33251E] to-[#0F0D0C]'],
                ['kind' => 'Series', 'title' => 'Velvet Signal', 'genre' => 'Thriller / Sci-Fi', 'background' => 'from-[#1A1615] via-[#241C18] to-[#0F0D0C]'],
                ['kind' => 'Movie', 'title' => 'Ash Crown', 'genre' => 'Fantasy / Epic', 'background' => 'from-[#1A1615] via-[#2D201A] to-[#0F0D0C]'],
                ['kind' => 'Game', 'title' => 'Night Forge', 'genre' => 'Action / Strategy', 'background' => 'from-[#1A1615] via-[#35261D] to-[#0F0D0C]'],
                ['kind' => 'Movie', 'title' => 'Golden Static', 'genre' => 'Crime / Noir', 'background' => 'from-[#1A1615] via-[#2A1D18] to-[#0F0D0C]'],
                ['kind' => 'Series', 'title' => 'Cinder House', 'genre' => 'Family / Drama', 'background' => 'from-[#1A1615] via-[#251A16] to-[#0F0D0C]'],
                ['kind' => 'Game', 'title' => 'Torchline', 'genre' => 'Adventure / Co-op', 'background' => 'from-[#1A1615] via-[#36261C] to-[#0F0D0C]'],
            ];

            $adminLinks = [
                ['label' => 'Dashboard', 'active' => true],
                ['label' => 'Titles', 'active' => false],
                ['label' => 'Genres', 'active' => false],
                ['label' => 'Reports', 'active' => false],
                ['label' => 'Users', 'active' => false],
            ];
        @endphp

        <div class="ember-shell min-h-screen">
            <header class="sticky top-0 z-50 h-20 border-b border-[#FF6B00]/10 bg-[#0F0D0C]/90 backdrop-blur-md bg-[linear-gradient(180deg,rgba(15,13,12,0.95)_0%,rgba(15,13,12,0.84)_72%,rgba(15,13,12,0)_100%)]">
                <div class="mx-auto flex h-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <a href="{{ url('/') }}" class="flex items-baseline gap-1 whitespace-nowrap">
                        <span class="text-lg font-light tracking-tight text-[#F5F2F0] sm:text-xl">Check</span>
                        <span class="font-serif text-lg italic font-semibold text-[#FFA800] sm:text-xl">This Out</span>
                    </a>

                    <nav class="hidden items-center gap-8 md:flex">
                        <a href="#catalogue" class="text-sm font-medium text-[#948A85] transition-colors duration-300 hover:text-[#FF6B00]">Catalogue</a>
                        <a href="#featured" class="text-sm font-medium text-[#948A85] transition-colors duration-300 hover:text-[#FF6B00]">Featured</a>
                        <a href="#genres" class="text-sm font-medium text-[#948A85] transition-colors duration-300 hover:text-[#FF6B00]">Genres</a>
                        <a href="#footer" class="text-sm font-medium text-[#948A85] transition-colors duration-300 hover:text-[#FF6B00]">Community</a>
                    </nav>

                    <a href="#catalogue" class="rounded-lg bg-gradient-to-r from-[#FF6B00] to-[#FFA800] px-5 py-3 text-sm font-bold tracking-wide text-[#0F0D0C] transition-transform duration-300 hover:scale-[1.02]">
                        Browse Now
                    </a>
                </div>
            </header>

            <main class="mx-auto flex w-full max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:px-8">
                <aside class="hidden w-64 shrink-0 lg:block">
                    <div class="ember-ambient rounded-xl bg-[#1A1615] p-5">
                        <div class="mb-8">
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#948A85]">Admin Sidebar</p>
                            <h2 class="headline mt-3 text-3xl font-bold tracking-tight text-[#F5F2F0]">Control Room</h2>
                        </div>

                        <nav class="space-y-2">
                            @foreach ($adminLinks as $link)
                                <a href="#" class="flex items-center gap-3 rounded-md px-4 py-3 text-sm font-medium transition-colors duration-300 {{ $link['active'] ? 'bg-[#FF6B00]/10 text-[#FFA800]' : 'text-[#948A85] hover:bg-[#0F0D0C] hover:text-[#F5F2F0]' }}">
                                    <span class="h-2.5 w-2.5 rounded-full {{ $link['active'] ? 'bg-[#FF6B00]' : 'bg-[#948A85]/40' }}"></span>
                                    <span>{{ $link['label'] }}</span>
                                </a>
                            @endforeach
                        </nav>

                        <div class="mt-8 rounded-xl border border-[#948A85]/15 bg-[#0F0D0C] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#948A85]">Live signal</p>
                            <p class="headline mt-2 text-2xl font-bold text-[#F5F2F0]">36 premieres</p>
                            <p class="mt-1 text-sm text-[#948A85]">12 community spotlights active</p>
                        </div>
                    </div>
                </aside>

                <div class="min-w-0 flex-1 space-y-10">
                    <section id="featured" class="overflow-hidden rounded-xl border border-[#948A85]/10 bg-[#1A1615] ember-ambient">
                        <div class="grid gap-8 px-6 py-8 lg:grid-cols-[1.2fr_0.8fr] lg:px-10 lg:py-12">
                            <div class="space-y-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#FFA800]">Ember Cinema</p>
                                <h1 class="headline max-w-3xl text-4xl font-bold tracking-tight text-[#F5F2F0] sm:text-5xl lg:text-6xl">
                                    A cinematic entertainment platform with warmth, contrast, and a quiet glow.
                                </h1>
                                <p class="max-w-2xl text-base leading-7 text-[#948A85] sm:text-lg">
                                    Discover films and games inside a production-ready Blade layout shaped around ember tones, elegant type, and a relaxed premium presentation.
                                </p>

                                <div class="flex flex-wrap gap-3">
                                    <a href="#catalogue" class="rounded-lg bg-gradient-to-r from-[#FF6B00] to-[#FFA800] px-6 py-3 text-sm font-bold tracking-wide text-[#0F0D0C] transition-transform duration-300 hover:scale-[1.02]">
                                        View Catalogue
                                    </a>
                                    <a href="#footer" class="rounded-lg border border-[#948A85]/30 bg-[#1A1615] px-6 py-3 text-sm font-bold tracking-wide text-[#F5F2F0] transition-colors duration-300 hover:border-[#FF6B00]">
                                        Community Spotlight
                                    </a>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-3">
                                    <div class="rounded-xl border border-[#948A85]/10 bg-[#0F0D0C] p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#948A85]">Movies</p>
                                        <p class="headline mt-2 text-2xl font-bold text-[#F5F2F0]">128</p>
                                    </div>
                                    <div class="rounded-xl border border-[#948A85]/10 bg-[#0F0D0C] p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#948A85]">Games</p>
                                        <p class="headline mt-2 text-2xl font-bold text-[#F5F2F0]">64</p>
                                    </div>
                                    <div class="rounded-xl border border-[#948A85]/10 bg-[#0F0D0C] p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#948A85]">Series</p>
                                        <p class="headline mt-2 text-2xl font-bold text-[#F5F2F0]">92</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                                <article class="rounded-xl border border-[#948A85]/10 bg-[#0F0D0C] p-5">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#948A85]">Tonight</p>
                                    <h2 class="headline mt-3 text-3xl font-bold tracking-tight text-[#F5F2F0]">Burning Horizon</h2>
                                    <p class="mt-2 text-sm text-[#948A85]">Thriller / Adventure / Drama</p>
                                </article>
                                <article class="rounded-xl border border-[#948A85]/10 bg-[#0F0D0C] p-5">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#948A85]">Top Game</p>
                                    <h2 class="headline mt-3 text-3xl font-bold tracking-tight text-[#F5F2F0]">Forge Run</h2>
                                    <p class="mt-2 text-sm text-[#948A85]">Action / Strategy / Co-op</p>
                                </article>
                            </div>
                        </div>
                    </section>

                    <section id="catalogue" class="space-y-6">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#948A85]">Catalogue</p>
                                <h2 class="headline mt-2 text-3xl font-bold tracking-tight text-[#F5F2F0]">Curated releases in a 4-column grid</h2>
                            </div>
                            <span class="hidden text-xs font-semibold uppercase tracking-[0.35em] text-[#948A85] sm:block">Elegant rounded cards with warm ember glow</span>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach ($catalogueItems as $item)
                                <article class="group overflow-hidden rounded-xl bg-[#1A1615] shadow-[0_0_0_1px_rgba(148,138,133,0.10)] transition-transform duration-300 hover:-translate-y-1 hover:shadow-[0_10px_25px_rgba(255,107,0,0.15)]">
                                    <div class="relative aspect-[2/3] overflow-hidden bg-gradient-to-br {{ $item['background'] }}">
                                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(255,168,0,0.16),transparent_30%),linear-gradient(180deg,transparent_0%,rgba(15,13,12,0.14)_50%,rgba(15,13,12,0.90)_100%)]"></div>
                                        <div class="absolute inset-x-0 bottom-0 p-4">
                                            <div class="flex items-center gap-2 text-[#FFA800]">
                                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0">
                                                    <path d="M10 1.75 12.48 6.8l5.56.81-4.02 3.92.95 5.54L10 14.46l-4.97 2.61.95-5.54L2 7.61l5.56-.81L10 1.75Z" />
                                                </svg>
                                                <h3 class="headline text-2xl font-bold tracking-tight text-[#F5F2F0]">{{ $item['title'] }}</h3>
                                            </div>
                                            <p class="mt-2 text-sm text-[#948A85]">{{ $item['kind'] }} / {{ $item['genre'] }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-3 p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-xs font-semibold uppercase tracking-[0.28em] text-[#948A85]">{{ $item['genre'] }}</span>
                                            <span class="text-xs font-semibold uppercase tracking-[0.28em] text-[#FFA800]">Featured</span>
                                        </div>
                                        <button type="button" class="rounded-lg bg-gradient-to-r from-[#FF6B00] to-[#FFA800] px-4 py-3 text-sm font-bold tracking-wide text-[#0F0D0C] transition-transform duration-300 hover:scale-[1.01]">
                                            Play Trailer
                                        </button>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>

                    <section id="genres" class="grid gap-5 lg:grid-cols-3">
                        <article class="rounded-xl border border-[#948A85]/10 bg-[#1A1615] p-6 ember-ambient">
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#948A85]">Mood</p>
                            <h3 class="headline mt-3 text-2xl font-bold tracking-tight text-[#F5F2F0]">Noir</h3>
                            <p class="mt-2 text-sm text-[#948A85]">Suspense, shadows, and a warm amber underline.</p>
                        </article>
                        <article class="rounded-xl border border-[#948A85]/10 bg-[#1A1615] p-6 ember-ambient">
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#948A85]">Mood</p>
                            <h3 class="headline mt-3 text-2xl font-bold tracking-tight text-[#F5F2F0]">Adventure</h3>
                            <p class="mt-2 text-sm text-[#948A85]">Journey-driven films and games with luminous pacing.</p>
                        </article>
                        <article class="rounded-xl border border-[#948A85]/10 bg-[#1A1615] p-6 ember-ambient">
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#948A85]">Mood</p>
                            <h3 class="headline mt-3 text-2xl font-bold tracking-tight text-[#F5F2F0]">Epic</h3>
                            <p class="mt-2 text-sm text-[#948A85]">Big-screen scale with a softer cinematic glow.</p>
                        </article>
                    </section>
                </div>
            </main>

            <footer id="footer" class="mt-4 bg-[#1A1615]">
                <div class="h-[2px] bg-gradient-to-r from-[#FF6B00] via-[#FFA800] to-[#FF6B00]"></div>
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-[#948A85]">Community Spotlight</p>
                            <h2 class="headline mt-3 text-3xl font-bold tracking-tight text-[#F5F2F0]">Shared picks, reviews, and a warm evening crowd.</h2>
                            <p class="mt-3 max-w-2xl text-sm leading-7 text-[#948A85]">
                                Ember Cinema keeps the tone intimate and premium while surfacing the people, titles, and conversations that keep the platform active.
                            </p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-xl border border-[#948A85]/10 bg-[#0F0D0C] p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#948A85]">Top reviewer</p>
                                <p class="headline mt-2 text-2xl font-bold text-[#F5F2F0]">Mara Voss</p>
                                <p class="mt-2 text-sm text-[#948A85]">“The warm palette makes browsing feel like a screening lounge.”</p>
                            </div>
                            <div class="rounded-xl border border-[#948A85]/10 bg-[#0F0D0C] p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#948A85]">This week</p>
                                <p class="headline mt-2 text-2xl font-bold text-[#F5F2F0]">18 spotlights</p>
                                <p class="mt-2 text-sm text-[#948A85]">New drops, featured lists, and community favorites.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col gap-4 border-t border-[#948A85]/10 pt-6 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-[#948A85]">Check This Out • Ember Cinema interface</p>
                        <div class="flex flex-wrap gap-3">
                            <a href="#catalogue" class="rounded-lg bg-gradient-to-r from-[#FF6B00] to-[#FFA800] px-5 py-3 text-sm font-bold tracking-wide text-[#0F0D0C] transition-transform duration-300 hover:scale-[1.02]">
                                Browse Titles
                            </a>
                            <a href="#featured" class="rounded-lg bg-[#1A1615] px-5 py-3 text-sm font-bold tracking-wide text-[#F5F2F0] border border-[#948A85]/30 transition-colors duration-300 hover:border-[#FF6B00]">
                                View Spotlight
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
