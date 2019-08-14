<?php

namespace ASP\Repository\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as IlluminateValidator;

trait Validator
{
    /**
     * The request to be validated
     *
     * @var Request
     */
    private static $request;

    /**
     * The base rules
     *
     * @var array
     */
    private static $baseRules;

    /**
     * Validate the create action
     *
     * @param Request $request
     *
     * @return bool|array
     */
    protected static function validateCreate(Request $request)
    {
        self::setRequest($request);

        return self::validate($request, self::getCreateRules());
    }

    /**
     * Validate the update action
     *
     * @param Request $request
     *
     * @return bool|array
     */
    protected static function validateUpdate(Request $request)
    {
        self::setRequest($request);

        return self::validate($request, self::getUpdateRules());
    }

    /**
     * Validate the delete action
     *
     * @param Request $request
     *
     * @return bool|array
     */
    protected static function validateDelete(Request $request)
    {
        self::setRequest($request);

        return self::validate($request, self::getDeleteRules());
    }

    /**
     * @param Request $request
     *
     * @return void
     */
    private static function setRequest(Request $request)
    {
        self::$request = $request;
    }

    /**
     * @return Request
     */
    private static function getRequest()
    {
        return self::$request;
    }

    /**
     * @return array
     */
    private static function getBaseRules()
    {
        return self::$baseRules;
    }

    /**
     * @param array $rules
     *
     * @return void
     */
    private static function setBaseRules($rules)
    {
        self::$baseRules = $rules;
    }

    /**
     * Validate a request against the defined rules
     *
     * @param Request $request
     * @param array $rules
     *
     * @return bool|array
     */
    private static function validate(Request $request, $rules)
    {
        $validator = IlluminateValidator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();

            return response()->json($errors);
        }

        return true;
    }
}
