<?php

namespace DigitalCoreHub\LaravelIpRestriction;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use DigitalCoreHub\LaravelIpRestriction\Commands\LaravelIpRestrictionCommand;

class LaravelIpRestrictionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-ip-restriction')
            ->hasConfigFile()
            ->hasCommand(LaravelIpRestrictionCommand::class);
    }

    public function bootingPackage()
    {
        app('router')->aliasMiddleware('restrict_to_ip', RestrictToSpecificIp::class);
    }
}
