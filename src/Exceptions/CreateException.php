<?php

namespace ASP\Repository\Exceptions;

class CreateException extends RepositoryException
{
    /**
     * @var string
     */
    protected $crud = 'repository::repository.error.create';
}
