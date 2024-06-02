<?php

namespace Homeful\Property\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Homeful\Property\Property
 */
class Property extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Homeful\Property\Property::class;
    }
}
