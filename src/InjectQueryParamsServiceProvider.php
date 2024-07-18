<?php

namespace Scheel\InjectQueryParams;

use Illuminate\Routing\Contracts\CallableDispatcher;
use Illuminate\Routing\Contracts\ControllerDispatcher;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class InjectQueryParamsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name('inject-query-params');
    }

    public function registeringPackage(): void
    {
        $this->app->singleton(CallableDispatcher::class, function ($app) {
            return new \Scheel\InjectQueryParams\CallableDispatcher($app);
        });
        $this->app->singleton(ControllerDispatcher::class, function ($app) {
            return new \Scheel\InjectQueryParams\ControllerDispatcher($app);
        });
    }
}
