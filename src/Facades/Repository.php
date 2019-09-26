<?php

namespace ASP\Repository\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @package ASP\Repository\Facades
 */
class Repository extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'repository';
    }
}
