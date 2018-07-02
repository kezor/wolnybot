<?php

namespace App\Providers;

use App\Connector\ConnectorInterface;
use App\Connector\WolniFarmerzyConnector;
use App\Service\ActivitiesInterface;
use App\Service\ActivitiesService;
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
