<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Yajra\DataTables\Html\Builder;

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
        // Jika ingin menggunakan Vite, pastikan ini relevan dengan kebutuhan Anda
        if (class_exists(Builder::class)) {
            Builder::useVite();
        }
    }
}
