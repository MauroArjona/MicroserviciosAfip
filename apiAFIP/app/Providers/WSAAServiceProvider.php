<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\WsaaService;
class WSAAServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(WsaaService::class, function () {
            return new WsaaService();
        });
    }
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
