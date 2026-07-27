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
