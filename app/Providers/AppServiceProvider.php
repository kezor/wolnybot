<?php

namespace App\Providers;

use App\Connector\ConnectorInterface;
use App\Connector\WolniFarmerzyConnector;
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
            ConnectorInterface::class,
            WolniFarmerzyConnector::class
        );
    }
}
