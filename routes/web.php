<?php

use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\FaqSuggestionController as AdminFaqSuggestionController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\NewsImportController;
use App\Http\Controllers\Admin\TitleController as AdminTitleController;
use App\Http\Controllers\Admin\TitleImportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FaqSuggestionController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\GamingEntryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyListsController;
use App\Http\Controllers\NewsCommentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationSettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\TitleSubmissionController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news:slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/u/{user:username}', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Catalogue (public browse, auth-gated actions)
Route::get('/catalogue', [TitleController::class, 'index'])->name('catalogue');

Route::middleware('auth')->group(function () {
    Route::get('/catalogue/submit', [TitleSubmissionController::class, 'create'])->name('titles.submit');
    Route::post('/catalogue/submit', [TitleSubmissionController::class, 'store'])->name('titles.submit.store');
    Route::get('/catalogue/submit/search', [TitleSubmissionController::class, 'search'])->name('titles.submit.search');
});

Route::get('/catalogue/{title:slug}', [TitleController::class, 'show'])->name('titles.show');

Route::middleware('auth')->group(function () {
    Route::post('/catalogue/{title:slug}/reviews', [ReviewController::class, 'store'])->name('titles.reviews.store');
    Route::delete('/catalogue/{title:slug}/reviews/{review}', [ReviewController::class, 'destroy'])->name('titles.reviews.destroy');
    Route::post('/catalogue/{title:slug}/reviews/{review}/like', [ReviewController::class, 'like'])->name('titles.reviews.like');
    Route::delete('/catalogue/{title:slug}/reviews/{review}/like', [ReviewController::class, 'unlike'])->name('titles.reviews.unlike');

    Route::post('/catalogue/{title:slug}/watchlist', [WatchlistController::class, 'update'])->name('titles.watchlist.update');
    Route::delete('/catalogue/{title:slug}/watchlist', [WatchlistController::class, 'destroy'])->name('titles.watchlist.destroy');

    Route::post('/catalogue/{title:slug}/gaming', [GamingEntryController::class, 'update'])->name('titles.gaming.update');
    Route::delete('/catalogue/{title:slug}/gaming', [GamingEntryController::class, 'destroy'])->name('titles.gaming.destroy');

    Route::post('/catalogue/{title:slug}/favourite', [FavouriteController::class, 'store'])->name('titles.favourite.store');
    Route::delete('/catalogue/{title:slug}/favourite', [FavouriteController::class, 'destroy'])->name('titles.favourite.destroy');

    Route::get('/contact/messages', [ContactController::class, 'myMessages'])->name('contact.messages.index');
    Route::get('/contact/messages/{contactMessage}', [ContactController::class, 'show'])->name('contact.messages.show');

    Route::get('/my/lists', [MyListsController::class, 'index'])->name('lists.index');
    Route::get('/my/watchlist', [WatchlistController::class, 'mine'])->name('watchlist.mine');
    Route::get('/my/gaming-list', [GamingEntryController::class, 'mine'])->name('gaming.mine');
    Route::get('/my/favourites', [FavouriteController::class, 'mine'])->name('favourites.mine');

    Route::post('/users/{user}/block', [BlockController::class, 'store'])->name('users.block');
    Route::delete('/users/{user}/block', [BlockController::class, 'destroy'])->name('users.block.destroy');

    Route::post('/news/{news:slug}/comments', [NewsCommentController::class, 'store'])->name('news.comments.store');
    Route::delete('/news/{news:slug}/comments/{comment}', [NewsCommentController::class, 'destroy'])->name('news.comments.destroy');

    Route::post('/faq/suggestions', [FaqSuggestionController::class, 'store'])->name('faq.suggestions.store');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notification-settings', [NotificationSettingController::class, 'update'])->name('notification-settings.update');

    Route::post('/theme', [ThemeController::class, 'update'])->name('theme.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/news', [AdminNewsController::class, 'index'])->name('news.index');
    Route::post('/news/import/start', [NewsImportController::class, 'start'])->name('news.import.start');
    Route::post('/news/import/{session}/step', [NewsImportController::class, 'step'])->name('news.import.step');
    Route::post('/news/import/{session}/cancel', [NewsImportController::class, 'cancel'])->name('news.import.cancel');
    Route::get('/news/create', [AdminNewsController::class, 'create'])->name('news.create');
    Route::post('/news', [AdminNewsController::class, 'store'])->name('news.store');
    Route::get('/news/{news}/edit', [AdminNewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{news}', [AdminNewsController::class, 'update'])->name('news.update');
    Route::delete('/news/{news}', [AdminNewsController::class, 'destroy'])->name('news.destroy');

    Route::get('/faq', [AdminFaqController::class, 'index'])->name('faq.index');
    Route::post('/faq/categories', [AdminFaqController::class, 'storeCategory'])->name('faq.categories.store');
    Route::patch('/faq/categories/{faqCategory}', [AdminFaqController::class, 'updateCategory'])->name('faq.categories.update');
    Route::delete('/faq/categories/{faqCategory}', [AdminFaqController::class, 'destroyCategory'])->name('faq.categories.destroy');
    Route::post('/faq/items', [AdminFaqController::class, 'storeItem'])->name('faq.items.store');
    Route::patch('/faq/items/{faqItem}', [AdminFaqController::class, 'updateItem'])->name('faq.items.update');
    Route::delete('/faq/items/{faqItem}', [AdminFaqController::class, 'destroyItem'])->name('faq.items.destroy');

    Route::patch('/faq/suggestions/{faqSuggestion}/resolve', [AdminFaqSuggestionController::class, 'resolve'])->name('faq.suggestions.resolve');
    Route::delete('/faq/suggestions/{faqSuggestion}', [AdminFaqSuggestionController::class, 'destroy'])->name('faq.suggestions.destroy');

    Route::get('/titles', [AdminTitleController::class, 'index'])->name('titles.index');
    Route::get('/titles/create', [AdminTitleController::class, 'create'])->name('titles.create');
    Route::post('/titles', [AdminTitleController::class, 'store'])->name('titles.store');
    Route::patch('/titles/{title}/approve', [AdminTitleController::class, 'approve'])->name('titles.approve');
    Route::post('/titles/{title}/reject', [AdminTitleController::class, 'reject'])->name('titles.reject');
    Route::delete('/titles/{title}', [AdminTitleController::class, 'destroy'])->name('titles.destroy');

    Route::post('/titles/import/start', [TitleImportController::class, 'start'])->name('titles.import.start');
    Route::post('/titles/import/{session}/step', [TitleImportController::class, 'step'])->name('titles.import.step');
    Route::post('/titles/import/{session}/cancel', [TitleImportController::class, 'cancel'])->name('titles.import.cancel');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/contact-messages', [AdminContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::patch('/contact-messages/{contactMessage}/toggle-read', [AdminContactMessageController::class, 'toggleRead'])->name('contact-messages.toggle-read');
    Route::post('/contact-messages/{contactMessage}/reply', [AdminContactMessageController::class, 'reply'])->name('contact-messages.reply');
    Route::delete('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
});

require __DIR__.'/auth.php';
