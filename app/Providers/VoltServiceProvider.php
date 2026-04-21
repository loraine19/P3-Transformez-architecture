<?php
// Service provider - registers Volt (functional Livewire) view paths
// Volt lets you write Livewire components in a single .blade.php file

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;

/* CLASS */
class VoltServiceProvider extends ServiceProvider
{
    /* PUBLIC METHOD */
    /* register */
    public function register(): void
    {
        //
    }

    /* PUBLIC METHOD */
    /* boot */
    public function boot(): void
    {
        // tell Volt where to scan for .blade.php component files
        Volt::mount([
            config('livewire.view_path', resource_path('views/livewire')),
            resource_path('views/pages'),
        ]);
    }
}
