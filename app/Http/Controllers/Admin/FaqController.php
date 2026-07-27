<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use App\Models\FaqItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $categories = FaqCategory::with(['faqItems' => fn ($query) => $query->orderBy('order')])
            ->orderBy('order')
            ->get();

        return view('admin.faq.index', ['categories' => $categories]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
        ]);

        FaqCategory::create([
            'name' => $validated['name'],
            'order' => $validated['order'] ?? 0,
        ]);

        return back()->with('status', 'faq-category-created');
    }

    public function updateCategory(Request $request, FaqCategory $faqCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
        ]);

        $faqCategory->update([
            'name' => $validated['name'],
            'order' => $validated['order'] ?? 0,
        ]);

        return back()->with('status', 'faq-category-updated');
    }

    public function destroyCategory(FaqCategory $faqCategory): RedirectResponse
    {
        $faqCategory->delete();

        return back()->with('status', 'faq-category-deleted');
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'faq_category_id' => ['required', 'exists:faq_categories,id'],
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
            'order' => ['nullable', 'integer'],
        ]);

        FaqItem::create([
            'faq_category_id' => $validated['faq_category_id'],
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'order' => $validated['order'] ?? 0,
        ]);

        return back()->with('status', 'faq-item-created');
    }

    public function updateItem(Request $request, FaqItem $faqItem): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
            'order' => ['nullable', 'integer'],
        ]);

        $faqItem->update([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'order' => $validated['order'] ?? 0,
        ]);

        return back()->with('status', 'faq-item-updated');
    }

    public function destroyItem(FaqItem $faqItem): RedirectResponse
    {
        $faqItem->delete();

        return back()->with('status', 'faq-item-deleted');
    }
}
