<?php

namespace App\Providers;
use App\Interfaces\CronServiceInterface;
use App\Services\CronService;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            CronServiceInterface::class,
            CronService::class
        );
    }
}
