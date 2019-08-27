<?php

namespace ASP\Repository\Exceptions;

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

        /* Status code set to 422 to mainting consistency with Laravel, see:
         * https://laravel.com/docs/5.8/validation#quick-ajax-requests-and-validation
         */
        parent::__construct(null, UNPROCESSABLE, $message, $errors);
    }
}
