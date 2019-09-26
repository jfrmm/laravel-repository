<?php

namespace ASP\Repository\Traits;

use Illuminate\Http\Request;

/**
 * @package ASP\Repository\Traits
 */
trait ValidatorRules
{
    /**
     * The base rules
     *
     * @var array
     */
    private static $baseRules;

    /**
     * We can use Laravel's own boot() method available for all
     * Eloquent Models to set the validation rules of the Model
     *
     * @link https://laravel.com/api/5.8/Illuminate/Database/Eloquent/Model.html#method_boot
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::$baseRules = [];
    }

    /**
     * Model base rules
     * Override when needed
     *
     * @param Request $request
     *
     * @return array
     */
    protected static function getBaseRules(Request $request)
    {
        return self::$baseRules;
    }

    /**
     * Model create rules
     * Override when needed
     *
     * @param Request $request
     *
     * @return array
     */
    protected static function getCreateRules(Request $request)
    {
        return self::getBaseRules($request);
    }

    /**
     * Model update rules
     * Override when needed
     *
     * @param Request $request
     *
     * @return array
     */
    protected static function getUpdateRules(Request $request)
    {
        return self::getBaseRules($request);
    }

    /**
     * Model delete rules
     * Override when needed
     *
     * @param Request $request
     *
     * @return array
     */
    protected static function getDeleteRules(Request $request)
    {
        return [];
    }
}
