<?php

namespace ASP\Repository\Exceptions;

use ASP\Repository\Exceptions\RepositoryException;

class ValidationException extends RepositoryException
{
    /**
     * The validation errors
     *
     * @var array
     */
    private $validationErrors;

    /**
     * Constructor.
     *
     * @param string       $message
     * @param array|null   $errors
     */
    public function __construct($message, $errors = null)
    {
        $this->message = $message;
        $this->report($this->message);

        $this->validationErrors = $errors;
    }

    /**
     * Get the validation errors
     *
     * @return array|null
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }
}
