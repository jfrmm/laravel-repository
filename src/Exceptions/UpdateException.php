<?php

namespace ASP\Repository\Exceptions;

class UpdateException extends RepositoryException
{
    /**
     * @var string
     */
    protected $crud = 'repository::repository.error.update';
}
