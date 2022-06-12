<?php

namespace App\Providers;

use App\Services\ApiRequestServices;
use Illuminate\Support\ServiceProvider;

class ApiRequestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ApiRequestServices::class, function($app){
            return new ApiRequestServices();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
