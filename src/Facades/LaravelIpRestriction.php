<?php

namespace DigitalCoreHub\LaravelIpRestriction\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DigitalCoreHub\LaravelIpRestriction\LaravelIpRestriction
 */
class LaravelIpRestriction extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \DigitalCoreHub\LaravelIpRestriction\LaravelIpRestriction::class;
    }
}
