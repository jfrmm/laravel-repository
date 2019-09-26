<?php

namespace ASP\Repository\Base;

use ReflectionClass;
use ReflectionException;

/**
 * Class Model
 *
 * This Class has a couple of methods that implement late static binding, this is
 * needed to have a static method to find out the extending class name. See:
 * https://www.php.net/manual/en/language.oop5.late-static-bindings.php
 *
 * @package ASP\Repository\Base
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Get Model name through reflection
     *
     * @return string
     *
     * @throws ReflectionException
     */
    public static function getModelName()
    {
        return (new ReflectionClass(get_called_class()))->getShortName();
    }
}
