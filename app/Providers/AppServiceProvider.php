<?php
// Service provider - main app bootstrap file
// register = bind things in container, boot = runs after all providers loaded

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/* CLASS */
class AppServiceProvider extends ServiceProvider
{
    /* PUBLIC METHOD */
    /* register */
    public function register(): void
    {
        // bind interfaces to implementations here - dependency injection container config
    }

    /* PUBLIC METHOD */
    /* boot */
    public function boot(): void
    {
        // define the 'api' rate limiter used by throttleApi() in bootstrap/app.php
        // 60 requests per minute per IP - adjust limit as needed
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}
