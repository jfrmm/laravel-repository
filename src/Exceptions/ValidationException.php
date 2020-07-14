<?php

namespace ASP\Repository\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * @package ASP\Repository\Exceptions
 */
class ValidationException extends RepositoryException
{
    /**
     * Constructor.
     *
     * @param string       $message
     * @param array|null   $errors
     */
    public function __construct($message, $errors = null)
    {
        $this->message = $message;

        /* Status code set to 422 to maintain consistency with Laravel, see:
         * https://laravel.com/docs/5.8/validation#quick-ajax-requests-and-validation
         */
        parent::__construct(null, Response::HTTP_UNPROCESSABLE_ENTITY, $message, $errors);
    }

    /**
     * Report the exception to the log
     *
     * @return void
     */
    public function report()
    {
        $this->logValidationException();
    }

    /**
     * Log this validation exception in a readable manner
     *
     * @return void
     */
    private function logValidationException()
    {
        Log::error($this->message);
        Log::error(print_r($this->getExceptionData(), true));
    }
}
