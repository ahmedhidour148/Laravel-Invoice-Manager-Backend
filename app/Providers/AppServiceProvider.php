<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Observers\InvoiceObserve;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        Invoice::observe(new InvoiceObserve());
        RateLimiter::for('limit5', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });
        RateLimiter::for('Limiter-Resend-code', function (Request $request) {
            return Limit::perMinute(1)->by($request->ip());
        });
        RateLimiter::for('Limiter-Verfiy-email', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

    }
}
