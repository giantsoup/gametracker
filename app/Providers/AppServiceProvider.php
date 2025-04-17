<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        // Make Str available in Blade templates
        Blade::directive('str', function ($expression) {
            return "<?php echo Illuminate\\Support\\Str::$expression; ?>";
        });

        // Share the Str facade with all views
        view()->share('Str', Str::class);
    }
}
