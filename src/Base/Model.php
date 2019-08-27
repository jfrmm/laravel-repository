<?php

namespace ASP\Repository\Base;

use ReflectionClass;
use ReflectionException;

class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Get Model name through reflection
     *
     * @return string
     * @throws ReflectionException
     */
    public static function getModelName()
    {
        return (new ReflectionClass(self::class))->getShortName();
    }
}