<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Number;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Evita que Laravel llame a ext-intl para formatear números.
        // Number::format() usa intl internamente; con esto usamos el locale 'C' (estándar POSIX).
        Number::useLocale('en');

        // Compartir ajustes con todas las vistas del frontend
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $view->with('settings', \App\Models\SiteSetting::pluck('value', 'key')->all());
        });

        // Registrar observador de pedidos
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
    }
}

