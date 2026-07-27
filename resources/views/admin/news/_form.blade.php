@csrf

<div>
    <x-input-label for="title" value="Title" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $article->title ?? '')" required minlength="3" autofocus />
    <x-input-error class="mt-2" :messages="$errors->get('title')" />
</div>

<div>
    <x-input-label for="published_at" value="Publish date (leave empty to keep as draft)" />
    <x-text-input id="published_at" name="published_at" type="datetime-local" class="mt-1 block w-full" :value="old('published_at', isset($article->published_at) ? $article->published_at?->format('Y-m-d\TH:i') : '')" />
    <x-input-error class="mt-2" :messages="$errors->get('published_at')" />
</div>

<div>
    <x-input-label for="body" value="Content" />
    <textarea id="body" name="body" rows="10" class="mt-1 block w-full rounded-xl border px-4 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);" required minlength="10">{{ old('body', $article->body ?? '') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('body')" />
</div>

<div>
    <x-input-label for="cover_image" value="Cover image" />
    <input id="cover_image" name="cover_image" type="file" accept="image/*" class="mt-1 block w-full text-sm" />
    <x-input-error class="mt-2" :messages="$errors->get('cover_image')" />

    @if (! empty($article->cover_image))
        <img src="{{ asset('storage/'.$article->cover_image) }}" alt="Current cover" class="mt-3 h-24 w-40 rounded-xl object-cover" />
    @endif
</div>
