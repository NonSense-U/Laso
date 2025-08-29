<?php

namespace App\Providers;

use App\Helpers\medicationPriceHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Helpers\migrationsHelper;
use Closure;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //! Remove on production
        Model::unguard();
        $this->loadMigrationsFrom(migrationsHelper::load_migrations());
    }

    public function booted(Closure $callback)
    {
        medicationPriceHelper::getPrices();
    }
}
