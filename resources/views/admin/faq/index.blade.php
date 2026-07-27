@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.35em]" style="color: var(--theme-muted);">Admin</p>
            <h2 class="mt-2 font-[Bebas_Neue] text-4xl uppercase tracking-[0.18em]">Manage FAQ</h2>
        </div>

        @if (session('status'))
            <p class="rounded-xl border px-4 py-3 text-sm" style="border-color: var(--theme-border); color: var(--theme-muted);">Done.</p>
        @endif

        <form method="POST" action="{{ route('admin.faq.categories.store') }}" class="flex flex-wrap items-end gap-3 rounded-[1.75rem] border p-5" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border);">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <x-input-label value="New category name" />
                <x-text-input name="name" type="text" class="mt-1 block w-full" required />
            </div>
            <div class="w-28">
                <x-input-label value="Order" />
                <x-text-input name="order" type="number" class="mt-1 block w-full" value="0" />
            </div>
            <x-primary-button>Add category</x-primary-button>
        </form>

        @foreach ($categories as $category)
            <div class="rounded-[1.75rem] border p-6 space-y-4" style="background: color-mix(in srgb, var(--theme-surface) 92%, transparent); border-color: var(--theme-border); box-shadow: var(--theme-glow);">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <form method="POST" action="{{ route('admin.faq.categories.update', $category) }}" class="flex flex-wrap items-end gap-3">
                        @csrf
                        @method('PATCH')
                        <div>
                            <x-input-label value="Category" />
                            <x-text-input name="name" type="text" class="mt-1 block w-56" value="{{ $category->name }}" required />
                        </div>
                        <div class="w-24">
                            <x-input-label value="Order" />
                            <x-text-input name="order" type="number" class="mt-1 block w-full" value="{{ $category->order }}" />
                        </div>
                        <x-secondary-button>Save</x-secondary-button>
                    </form>
                    <form method="POST" action="{{ route('admin.faq.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category and all its questions?');">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>Delete category</x-danger-button>
                    </form>
                </div>

                <div class="space-y-3 border-t pt-4" style="border-color: var(--theme-border);">
                    @foreach ($category->faqItems as $item)
                        <div class="flex flex-col gap-3 rounded-xl border p-4 lg:flex-row lg:items-end" style="border-color: color-mix(in srgb, var(--theme-border) 70%, transparent);">
                            <form method="POST" action="{{ route('admin.faq.items.update', $item) }}" class="flex-1 space-y-2">
                                @csrf
                                @method('PATCH')
                                <x-input-label value="Question" />
                                <x-text-input name="question" type="text" class="block w-full" value="{{ $item->question }}" required />
                                <x-input-label value="Answer" />
                                <textarea name="answer" rows="2" class="block w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);" required>{{ $item->answer }}</textarea>
                                <div class="flex items-center gap-3">
                                    <input type="number" name="order" value="{{ $item->order }}" class="w-20 rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);" />
                                    <x-secondary-button>Save</x-secondary-button>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('admin.faq.items.destroy', $item) }}" onsubmit="return confirm('Delete this question?');">
                                @csrf
                                @method('DELETE')
                                <x-danger-button>Delete</x-danger-button>
                            </form>
                        </div>
                    @endforeach

                    <form method="POST" action="{{ route('admin.faq.items.store') }}" class="space-y-2 rounded-xl border border-dashed p-4" style="border-color: var(--theme-border);">
                        @csrf
                        <input type="hidden" name="faq_category_id" value="{{ $category->id }}" />
                        <x-input-label value="New question" />
                        <x-text-input name="question" type="text" class="block w-full" required />
                        <x-input-label value="Answer" />
                        <textarea name="answer" rows="2" class="block w-full rounded-xl border px-3 py-2 text-sm" style="border-color: var(--theme-border); background: color-mix(in srgb, var(--theme-surface) 92%, transparent); color: var(--theme-text);" required></textarea>
                        <x-primary-button>Add question</x-primary-button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
