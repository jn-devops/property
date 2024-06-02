<?php

namespace Homeful\Property;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Homeful\Property\Commands\PropertyCommand;

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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_property_table')
            ->hasCommand(PropertyCommand::class);
    }
}
