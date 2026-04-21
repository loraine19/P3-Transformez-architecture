<?php
// Service provider - main app bootstrap file
// register = bind things in container, boot = runs after all providers loaded

namespace App\Providers;

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
        // good place for observers, gates, macros - runs after everything is loaded
    }
}
