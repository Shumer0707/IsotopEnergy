<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Inertia::share([
            'locale' => fn () => App::getLocale(),
            'translations' => fn () => __('messages'), // ✅ Передаем все переводы
            'cartFromServer' => fn () => Session::get('cart', []),
        ]);
    }
}
