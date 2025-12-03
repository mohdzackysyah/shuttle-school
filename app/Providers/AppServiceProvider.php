<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon; // Tambahkan library waktu

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
        // 1. Atur Lokalisasi Carbon ke Indonesia (Senin, Selasa, Januari...)
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
        
        // 2. Paksa Timezone ke Asia/Jakarta (WIB)
        date_default_timezone_set('Asia/Jakarta');
    }
}