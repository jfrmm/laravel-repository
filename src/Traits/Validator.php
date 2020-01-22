<?php

namespace ASP\Repository\Traits;

use ASP\Repository\Exceptions\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as IlluminateValidator;

/**
 * @package ASP\Repository\Traits
 */
trait Validator
{
    /**
     * Validate a request against the defined rules
     *
     * @param Request $request
     * @param array   $rules
     *
     * @return bool|array
     */
    private static function validate(Request $request, $rules)
    {
        $validator = IlluminateValidator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();

            return new ValidationException('Validation failed', $errors);
        }

        return true;
    }

    /**
     * Validate the create action
     * Override when needed
     *
     * @param Request $request
     *
     * @return bool|array
     */
    protected static function validateCreate(Request $request)
    {
        return self::validate($request, self::getCreateRules($request));
    }

    /**
     * Validate the update action
     * Override when needed
     *
     * @param Request $request
     *
     * @return bool|array
     */
    protected static function validateUpdate(Request $request)
    {
        return self::validate($request, self::getUpdateRules($request));
    }

    /**
     * Validate the delete action
     * Override when needed
     *
     * @param Request $request
     *
     * @return bool|array
     */
    protected static function validateDelete(Request $request)
    {
        return self::validate($request, self::getDeleteRules($request));
    }
}
