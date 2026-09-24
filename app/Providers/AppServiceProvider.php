<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        \Carbon\Carbon::setLocale(config('app.locale', 'id'));
        try {
            \Illuminate\Support\Facades\App::setLocale(config('app.locale', 'id'));
        } catch (\Throwable) {
            //
        }

        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp. ' . number_format($expression,0,',','.'); ?>";
        });
    }
}
