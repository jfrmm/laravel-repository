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

        parent::__construct(null, null, $message, $errors);
    }
}
