<?php

namespace ASP\Repository\Exceptions;

class ReadException extends RepositoryException
{
    /**
     * @var string
     */
    protected $crud = 'repository::repository.error.read';
}
