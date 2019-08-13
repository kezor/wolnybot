<?php

namespace App\Providers;

use App\Connector\ConnectorInterface;
use App\Connector\WolniFarmerzyConnector;
use App\Service\ActivitiesInterface;
use App\Service\ActivitiesService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
Schema::defaultStringLength(191);
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
