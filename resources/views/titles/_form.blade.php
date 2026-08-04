@csrf

<div>
    <x-input-label for="title" value="Title" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required minlength="2" autofocus />
    <x-input-error class="mt-2" :messages="$errors->get('title')" />
</div>

<div>
    <x-input-label for="type" value="Type" />
    <select id="type" name="type" required class="mt-1 block w-full rounded-xl border px-4 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">
        <option value="">Choose a type...</option>
        @foreach (['movie' => 'Movie', 'series' => 'Series', 'anime' => 'Anime', 'game' => 'Game'] as $value => $label)
            <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('type')" />
</div>

<input type="hidden" name="external_id" id="external_id_input" value="{{ old('external_id') }}" />

<div id="external-search" class="hidden space-y-3 rounded-xl border p-4" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 96%, transparent);">
    <div class="flex items-center justify-between">
        <p class="text-xs font-bold uppercase tracking-[0.2em]" style="color: var(--theme-muted);">Search external database (optional)</p>
        <button type="button" id="external-clear" class="hidden text-xs underline" style="color: var(--theme-muted);">Clear selection</button>
    </div>
    <div class="flex gap-2">
        <input type="text" id="external-query" placeholder="Search by title..." class="block w-full rounded-xl border px-4 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);" />
        <button type="button" id="external-search-btn" class="shrink-0 rounded-xl border px-4 py-2 text-sm font-semibold transition-all duration-300" style="border-color: var(--theme-border);">Search</button>
    </div>
    <div id="external-results" class="grid grid-cols-2 gap-3 sm:grid-cols-4"></div>
    <div id="external-selected" class="hidden items-center gap-3 rounded-xl border p-3" style="border-color: var(--theme-border);">
        <img id="external-selected-poster" class="h-16 w-12 rounded-lg object-cover" alt="" />
        <p class="text-sm">Using external data for <span id="external-selected-title" class="font-semibold"></span></p>
    </div>
    <p class="text-xs" style="color: var(--theme-muted);">Movies and series search TMDB, anime searches AniList, games search IGDB. Pick a result to auto-fill the synopsis, release date and poster below. You can still override the cover image by uploading your own.</p>
</div>

<div>
    <x-input-label for="release_date" value="Release date" />
    <x-text-input id="release_date" name="release_date" type="date" class="mt-1 block w-full" :value="old('release_date')" />
    <x-input-error class="mt-2" :messages="$errors->get('release_date')" />
</div>

<div>
    <x-input-label for="synopsis" value="Synopsis" />
    <textarea id="synopsis" name="synopsis" rows="4" class="mt-1 block w-full rounded-xl border px-4 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);">{{ old('synopsis') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('synopsis')" />
</div>

<div>
    <x-input-label for="cover_image" value="Cover image" />
    <input id="cover_image" name="cover_image" type="file" accept="image/*" class="mt-1 block w-full text-sm" />
    <x-input-error class="mt-2" :messages="$errors->get('cover_image')" />
</div>

<div>
    <x-input-label value="Genres" />
    <div class="mt-2 flex flex-wrap gap-2">
        @foreach ($genres as $genre)
            <label class="flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs" style="border-color: var(--theme-border);">
                <input type="checkbox" name="genres[]" value="{{ $genre->id }}" {{ collect(old('genres'))->contains($genre->id) ? 'checked' : '' }} />
                {{ $genre->name }}
            </label>
        @endforeach
    </div>
</div>

<div>
    <x-input-label value="Platforms (games only)" />
    <div class="mt-2 flex flex-wrap gap-2">
        @foreach ($platforms as $platform)
            <label class="flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs" style="border-color: var(--theme-border);">
                <input type="checkbox" name="platforms[]" value="{{ $platform->id }}" {{ collect(old('platforms'))->contains($platform->id) ? 'checked' : '' }} />
                {{ $platform->name }}
            </label>
        @endforeach
    </div>
</div>

<script>
    (() => {
        const typeSelect = document.getElementById('type');
        const searchBox = document.getElementById('external-search');
        const queryInput = document.getElementById('external-query');
        const searchBtn = document.getElementById('external-search-btn');
        const resultsBox = document.getElementById('external-results');
        const externalIdInput = document.getElementById('external_id_input');
        const selectedBox = document.getElementById('external-selected');
        const selectedPoster = document.getElementById('external-selected-poster');
        const selectedTitle = document.getElementById('external-selected-title');
        const clearBtn = document.getElementById('external-clear');
        const titleInput = document.getElementById('title');
        const synopsisInput = document.getElementById('synopsis');
        const releaseDateInput = document.getElementById('release_date');

        const searchableTypes = ['movie', 'series', 'anime', 'game'];
        const searchUrl = '{{ route('titles.submit.search') }}';

        const setMessage = (text) => {
            resultsBox.innerHTML = '';
            const message = document.createElement('p');
            message.className = 'col-span-full text-xs';
            message.style.color = 'var(--theme-muted)';
            message.textContent = text;
            resultsBox.appendChild(message);
        };

        const clearSelection = () => {
            externalIdInput.value = '';
            selectedBox.classList.add('hidden');
            clearBtn.classList.add('hidden');
            resultsBox.innerHTML = '';
            queryInput.value = '';
        };

        clearBtn.addEventListener('click', clearSelection);

        const toggleSearchBox = () => {
            searchBox.classList.toggle('hidden', !searchableTypes.includes(typeSelect.value));
        };

        typeSelect.addEventListener('change', () => {
            toggleSearchBox();
            clearSelection();
        });
        toggleSearchBox();

        const selectResult = (result) => {
            externalIdInput.value = result.id;
            titleInput.value = result.title;
            synopsisInput.value = result.overview ?? '';
            releaseDateInput.value = result.release_date ?? '';
            selectedTitle.textContent = result.title + (result.year ? ` (${result.year})` : '');
            selectedPoster.src = result.poster_url ?? '';
            selectedBox.classList.remove('hidden');
            selectedBox.classList.add('flex');
            clearBtn.classList.remove('hidden');
            resultsBox.innerHTML = '';
        };

        const renderResults = (results) => {
            resultsBox.innerHTML = '';

            if (results.length === 0) {
                setMessage('No matches found. You can still fill in the form manually.');
                return;
            }

            results.forEach((result) => {
                const card = document.createElement('button');
                card.type = 'button';
                card.className = 'flex flex-col overflow-hidden rounded-xl border text-left transition-all duration-300 hover:opacity-80';
                card.style.borderColor = 'var(--theme-border)';

                if (result.poster_url) {
                    const img = document.createElement('img');
                    img.src = result.poster_url;
                    img.alt = '';
                    img.className = 'h-32 w-full object-cover';
                    card.appendChild(img);
                } else {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'flex h-32 w-full items-center justify-center text-xs';
                    placeholder.style.color = 'var(--theme-muted)';
                    placeholder.textContent = 'No poster';
                    card.appendChild(placeholder);
                }

                const label = document.createElement('span');
                label.className = 'p-2 text-xs font-semibold';
                label.textContent = result.title + (result.year ? ` (${result.year})` : '');
                card.appendChild(label);

                card.addEventListener('click', () => selectResult(result));
                resultsBox.appendChild(card);
            });
        };

        const runSearch = async () => {
            const query = queryInput.value.trim();
            if (query.length < 2) {
                return;
            }

            setMessage('Searching...');

            const url = new URL(searchUrl, window.location.origin);
            url.searchParams.set('type', typeSelect.value);
            url.searchParams.set('q', query);

            try {
                const response = await fetch(url, { headers: { Accept: 'application/json' } });

                if (!response.ok) {
                    throw new Error('Search failed');
                }

                const data = await response.json();

                if (data.unavailable) {
                    setMessage('This search service is temporarily unavailable. You can still fill in the form manually.');
                    return;
                }

                renderResults(data.results ?? []);
            } catch (error) {
                setMessage('Could not reach the external database. You can still fill in the form manually.');
            }
        };

        searchBtn.addEventListener('click', runSearch);
        queryInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                runSearch();
            }
        });
    })();
</script>
