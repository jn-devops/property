<?php

namespace Homeful\Property;

use Spatie\LaravelPackageTools\PackageServiceProvider;
use Homeful\Property\Commands\PropertyCommand;
use Spatie\LaravelPackageTools\Package;

class PropertyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('property')
            ->hasConfigFile(['property'])
            ->hasCommand(PropertyCommand::class);
    }
}
