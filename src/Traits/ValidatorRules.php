<?php

namespace ASP\Repository\Traits;

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

        self::setBaseRules([]);
    }

    /**
     * @param array $rules
     *
     * @return void
     */
    protected static function setBaseRules($rules)
    {
        self::$baseRules = $rules;
    }

    /**
     * @return array
     */
    protected static function getBaseRules()
    {
        return self::$baseRules;
    }

    /**
     * @return array
     */
    protected static function getCreateRules()
    {
        return self::getBaseRules();
    }

    /**
     * @return array
     */
    protected static function getUpdateRules()
    {
        return self::getBaseRules();
    }

    /**
     * @return array
     */
    protected static function getDeleteRules()
    {
        return [];
    }
}
