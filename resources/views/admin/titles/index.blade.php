@extends('layouts.admin')

@section('content')
    <div class="space-y-6" x-data="{ rejecting: null }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin</p>
                <h2 class="mt-2 font-bold font-[Fredoka] text-4xl">Manage Titles</h2>
            </div>
            <a href="{{ route('admin.titles.create') }}" class="inline-flex h-11 items-center rounded-xl border px-5 text-sm font-semibold uppercase tracking-[0.04em]" style="border-color: var(--theme-accent); color: var(--theme-accent);">+ New Title</a>
        </div>

        <div class="rounded-tl-[1.5rem] rounded-tr-[1.5rem] rounded-br-[1.5rem] rounded-bl-md border p-5" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Import from TMDB / AniList / IGDB</h3>
                <button type="button" id="import-toggle" class="text-sm underline" style="color: var(--theme-accent);">Show</button>
            </div>

            <div id="import-panel" class="hidden mt-4 space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-muted);">Types</p>
                    <div class="mt-2 flex flex-wrap gap-4">
                        @foreach (['movie' => 'Movies', 'series' => 'Series', 'anime' => 'Anime', 'game' => 'Games'] as $value => $label)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" class="import-type" value="{{ $value }}" checked />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-wrap gap-6">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-muted);">Genre (optional)</label>
                        <select id="import-genre" class="mt-1 block rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">
                            <option value="">Any genre</option>
                            @foreach ($importGenres as $name)
                                <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs" style="color: var(--theme-muted);">Not every genre is supported by every source — unsupported combinations just return unfiltered results for that type.</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-muted);">Mode</label>
                        <div class="mt-1 flex gap-4 text-sm">
                            <label class="flex items-center gap-1">
                                <input type="radio" name="import-mode" value="trending" checked /> Trending
                            </label>
                            <label class="flex items-center gap-1">
                                <input type="radio" name="import-mode" value="newest" /> Newest
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="import-count" class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-muted);">How many</label>
                        <input type="number" id="import-count" min="1" max="40" value="12" class="mt-1 block w-24 rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);" />
                        <p class="mt-1 text-xs" style="color: var(--theme-muted);">Split evenly across the types you picked.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" id="import-start-btn" class="rounded-xl px-4 py-2 text-sm font-semibold uppercase tracking-[0.04em] transition-all duration-300" style="background: var(--theme-accent); color: var(--theme-bg);">Start Import</button>
                    <button type="button" id="import-cancel-btn" class="hidden rounded-xl border px-4 py-2 text-sm font-semibold uppercase tracking-[0.04em]" style="border-color: var(--theme-border);">Cancel</button>
                </div>

                <div id="import-progress-wrap" class="hidden">
                    <div class="h-2 w-full overflow-hidden rounded-full" style="background: color-mix(in srgb, var(--theme-border) 60%, transparent);">
                        <div id="import-progress-bar" class="h-2 rounded-full transition-all duration-300" style="background: var(--theme-accent); width: 0%;"></div>
                    </div>
                    <p id="import-progress-text" class="mt-1 text-xs" style="color: var(--theme-muted);"></p>
                </div>

                <div id="import-log" class="max-h-48 space-y-1 overflow-y-auto rounded-xl border p-3 text-xs" style="border-color: var(--theme-border);"></div>
            </div>
        </div>

        <div class="flex gap-2 text-sm">
            @foreach (['pending' => 'Pending', 'accepted' => 'Accepted', 'rejected' => 'Rejected', 'all' => 'All'] as $value => $label)
                <a href="{{ route('admin.titles.index', ['status' => $value]) }}" class="rounded-xl border px-3 py-1.5 font-semibold" style="border-color: {{ $status === $value ? 'var(--theme-accent)' : 'var(--theme-border)' }}; color: {{ $status === $value ? 'var(--theme-accent)' : 'var(--theme-muted)' }};">{{ $label }}</a>
            @endforeach
        </div>

        <div class="space-y-4">
            @forelse ($titles as $title)
                <div class="rounded-tl-[1.5rem] rounded-tr-[1.5rem] rounded-br-[1.5rem] rounded-bl-md border p-5" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-[0.15em]" style="color: var(--theme-accent);">{{ ucfirst($title->type) }} · {{ ucfirst($title->status) }}</span>
                            <h3 class="mt-1 text-lg font-semibold">{{ $title->title }}</h3>
                            <p class="text-sm" style="color: var(--theme-muted);">
                                Submitted by {{ $title->submittedBy?->username ?? 'Admin' }}
                            </p>
                            @if ($title->status === 'rejected' && $title->rejection_reason)
                                <p class="mt-1 text-sm" style="color: var(--theme-muted);">Reason: {{ $title->rejection_reason }}</p>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @if ($title->status === 'accepted')
                                <a href="{{ route('titles.show', $title) }}" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">View</a>
                            @endif

                            @if ($title->status !== 'accepted')
                                <form method="POST" action="{{ route('admin.titles.approve', $title) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-accent); color: var(--theme-accent);">Approve</button>
                                </form>
                            @endif

                            @if ($title->status !== 'rejected')
                                <button type="button" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);" @click="rejecting === {{ $title->id }} ? rejecting = null : rejecting = {{ $title->id }}">Reject</button>
                            @endif

                            <form method="POST" action="{{ route('admin.titles.destroy', $title) }}" onsubmit="return confirm('Delete this title permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">Delete</button>
                            </form>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.titles.reject', $title) }}" class="mt-4" x-show="rejecting === {{ $title->id }}" x-transition>
                        @csrf
                        @method('POST')
                        <textarea name="rejection_reason" rows="2" required placeholder="Reason for rejection..." class="w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);"></textarea>
                        <button type="submit" class="mt-2 rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">Confirm rejection</button>
                    </form>
                </div>
            @empty
                <p style="color: var(--theme-muted);">Nothing here.</p>
            @endforelse
        </div>

        {{ $titles->links() }}
    </div>

    <script>
        (() => {
            const toggleBtn = document.getElementById('import-toggle');
            const panel = document.getElementById('import-panel');
            const startBtn = document.getElementById('import-start-btn');
            const cancelBtn = document.getElementById('import-cancel-btn');
            const progressWrap = document.getElementById('import-progress-wrap');
            const progressBar = document.getElementById('import-progress-bar');
            const progressText = document.getElementById('import-progress-text');
            const log = document.getElementById('import-log');
            const countInput = document.getElementById('import-count');
            const genreSelect = document.getElementById('import-genre');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const startUrl = '{{ route('admin.titles.import.start') }}';
            const stepUrl = (session) => `{{ url('/admin/titles/import') }}/${session}/step`;
            const cancelUrl = (session) => `{{ url('/admin/titles/import') }}/${session}/cancel`;

            let currentSession = null;
            let cancelled = false;

            toggleBtn.addEventListener('click', () => {
                panel.classList.toggle('hidden');
            });

            const addLogLine = (text, success) => {
                const line = document.createElement('p');
                line.textContent = text;
                line.style.color = success ? 'var(--theme-text)' : 'var(--theme-muted)';
                log.appendChild(line);
                log.scrollTop = log.scrollHeight;
            };

            const setProgress = (progress, total) => {
                const pct = total > 0 ? Math.round((progress / total) * 100) : 100;
                progressBar.style.width = `${pct}%`;
                progressText.textContent = `${progress} / ${total} imported`;
            };

            const finish = (message) => {
                cancelBtn.classList.add('hidden');
                startBtn.classList.remove('hidden');
                startBtn.disabled = false;
                if (message) {
                    addLogLine(message, true);
                }
            };

            const step = async () => {
                if (cancelled || !currentSession) {
                    return;
                }

                try {
                    const response = await fetch(stepUrl(currentSession), {
                        method: 'POST',
                        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    });

                    if (!response.ok) {
                        throw new Error('Step failed');
                    }

                    const data = await response.json();

                    data.imported.forEach((item) => {
                        addLogLine(`${item.success ? '✓' : '✗'} ${item.title} (${item.type})`, item.success);
                    });

                    setProgress(data.progress, data.total);

                    if (data.done) {
                        finish('Import complete.');
                        return;
                    }

                    step();
                } catch (error) {
                    finish('Import stopped — the connection to the server was lost.');
                }
            };

            startBtn.addEventListener('click', async () => {
                const types = Array.from(document.querySelectorAll('.import-type:checked')).map((el) => el.value);

                if (types.length === 0) {
                    addLogLine('Pick at least one type first.', false);
                    return;
                }

                const mode = document.querySelector('input[name="import-mode"]:checked').value;
                const genre = genreSelect.value;
                const count = countInput.value;

                cancelled = false;
                log.innerHTML = '';
                progressWrap.classList.remove('hidden');
                setProgress(0, count);
                startBtn.classList.add('hidden');
                cancelBtn.classList.remove('hidden');

                const body = new URLSearchParams();
                types.forEach((type) => body.append('types[]', type));
                body.append('mode', mode);
                body.append('count', count);
                if (genre) {
                    body.append('genre', genre);
                }

                try {
                    const response = await fetch(startUrl, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: body.toString(),
                    });

                    if (!response.ok) {
                        throw new Error('Start failed');
                    }

                    const data = await response.json();
                    currentSession = data.session;
                    setProgress(0, data.total);

                    if (data.total === 0) {
                        finish('Nothing new to import — everything matching was already in the catalogue.');
                        return;
                    }

                    step();
                } catch (error) {
                    finish('Could not start the import.');
                }
            });

            cancelBtn.addEventListener('click', async () => {
                cancelled = true;

                if (currentSession) {
                    await fetch(cancelUrl(currentSession), {
                        method: 'POST',
                        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    });
                }

                finish('Import cancelled — titles imported so far were kept.');
            });
        })();
    </script>
@endsection
