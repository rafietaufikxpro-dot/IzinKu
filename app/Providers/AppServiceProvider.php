<?php

namespace App\Providers;

use App\Contracts\FingerprintDataSource;
use App\Services\Fingerprint\SimulasiFingerprintDataSource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FingerprintDataSource::class, SimulasiFingerprintDataSource::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
