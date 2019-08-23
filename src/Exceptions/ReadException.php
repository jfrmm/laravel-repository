<?php

namespace ASP\Repository\Exceptions;

class ReadException extends RepositoryException
{
    /**
     * @var string
     */
    protected $crud = 'crud.error.read';
}
