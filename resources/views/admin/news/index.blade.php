@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin</p>
                <h2 class="mt-2 font-bold font-[Fredoka] text-4xl">Manage News</h2>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="button" id="news-import-toggle" class="inline-flex h-11 items-center rounded-xl border px-5 text-sm font-semibold uppercase tracking-[0.04em]" style="border-color: var(--theme-border); color: var(--theme-text);">Import from RSS</button>
                <a href="{{ route('admin.news.create') }}" class="inline-flex h-11 items-center rounded-xl border px-5 text-sm font-semibold uppercase tracking-[0.04em]" style="border-color: var(--theme-accent); color: var(--theme-accent);">+ New Article</a>
            </div>
        </div>

        <div id="news-import-panel" class="hidden rounded-tl-[1.5rem] rounded-tr-[1.5rem] rounded-br-[1.5rem] rounded-bl-md border p-5" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <div class="flex items-center gap-3">
                <button type="button" id="news-import-start-btn" class="rounded-xl px-4 py-2 text-sm font-semibold uppercase tracking-[0.04em] transition-all duration-300" style="background: var(--theme-accent); color: var(--theme-bg);">Start Import</button>
                <button type="button" id="news-import-cancel-btn" class="hidden rounded-xl border px-4 py-2 text-sm font-semibold uppercase tracking-[0.04em]" style="border-color: var(--theme-border);">Cancel</button>
            </div>

            <div id="news-import-progress-wrap" class="mt-4 hidden">
                <div class="h-2 w-full overflow-hidden rounded-full" style="background: color-mix(in srgb, var(--theme-border) 60%, transparent);">
                    <div id="news-import-progress-bar" class="h-2 rounded-full transition-all duration-300" style="background: var(--theme-accent); width: 0%;"></div>
                </div>
                <p id="news-import-progress-text" class="mt-1 text-xs" style="color: var(--theme-muted);"></p>
            </div>

            <div id="news-import-log" class="mt-4 max-h-48 space-y-1 overflow-y-auto rounded-xl border p-3 text-xs" style="border-color: var(--theme-border);"></div>
        </div>

        <div class="overflow-x-auto rounded-tl-[1.75rem] rounded-tr-[1.75rem] rounded-br-[1.75rem] rounded-bl-md border" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b text-xs uppercase tracking-[0.15em]" style="border-color: var(--theme-border); color: var(--theme-muted);">
                        <th class="px-5 py-4">Title</th>
                        <th class="px-5 py-4">Published</th>
                        <th class="px-5 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articles as $article)
                        <tr class="border-b" style="border-color: color-mix(in srgb, var(--theme-border) 60%, transparent);">
                            <td class="px-5 py-4 font-semibold">{{ $article->title }}</td>
                            <td class="px-5 py-4" style="color: var(--theme-muted);">{{ $article->published_at?->format('M j, Y') ?? 'Draft' }}</td>
                            <td class="px-5 py-4 text-right space-x-2">
                                <a href="{{ route('admin.news.edit', $article) }}" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">Edit</a>
                                <form method="POST" action="{{ route('admin.news.destroy', $article) }}" class="inline" onsubmit="return confirm('Delete this article?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl border px-3 py-1.5 text-xs font-semibold uppercase" style="border-color: var(--theme-border);">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $articles->links() }}
    </div>

    <script>
        (() => {
            const toggleBtn = document.getElementById('news-import-toggle');
            const panel = document.getElementById('news-import-panel');
            const startBtn = document.getElementById('news-import-start-btn');
            const cancelBtn = document.getElementById('news-import-cancel-btn');
            const progressWrap = document.getElementById('news-import-progress-wrap');
            const progressBar = document.getElementById('news-import-progress-bar');
            const progressText = document.getElementById('news-import-progress-text');
            const log = document.getElementById('news-import-log');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const startUrl = '{{ route('admin.news.import.start') }}';
            const stepUrl = (session) => `{{ url('/admin/news/import') }}/${session}/step`;
            const cancelUrl = (session) => `{{ url('/admin/news/import') }}/${session}/cancel`;

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
                        addLogLine(`${item.success ? '✓' : '✗'} ${item.title}`, item.success);
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
                log.innerHTML = '';
                progressWrap.classList.remove('hidden');
                setProgress(0, 0);
                startBtn.classList.add('hidden');
                cancelBtn.classList.remove('hidden');
                cancelled = false;

                try {
                    const response = await fetch(startUrl, {
                        method: 'POST',
                        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    });

                    if (!response.ok) {
                        throw new Error('Start failed');
                    }

                    const data = await response.json();
                    currentSession = data.session;
                    setProgress(0, data.total);

                    if (data.total === 0) {
                        finish('Nothing new to import — everything from the feeds was already in the catalogue.');
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

                finish('Import cancelled — articles imported so far were kept.');
            });
        })();
    </script>
@endsection
