<?php

namespace App\Providers;

use App\Channels\WhatsAppChannel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

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
        // Set the default string length for database schemas
        Schema::defaultStringLength(191);

        // Use Bootstrap for pagination views
        Paginator::useBootstrap();

        // Set timezone default ke Jakarta
        date_default_timezone_set('Asia/Jakarta');

        // Konfigurasi Carbon untuk Indonesia
        Carbon::setLocale('id');

        // Pastikan timestamps disimpan dalam timezone lokal
        DB::statement("SET time_zone = '+07:00'");

        // Register custom WhatsApp notification channel
        Notification::extend('whatsapp', function ($app) {
            return new WhatsAppChannel();
        });
    }
}
