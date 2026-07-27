<?php

namespace App\Http\View\Composers;

use App\Services\ThemeService;
use Illuminate\View\View;

class ThemeComposer
{
    public function __construct(private ThemeService $themeService)
    {
    }

    public function compose(View $view): void
    {
        $view->with([
            'theme' => $this->themeService->current(auth()->user()),
            'themeOptions' => $this->themeService->options(),
        ]);
    }
}