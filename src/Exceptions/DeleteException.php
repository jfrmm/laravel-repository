<?php

namespace ASP\Repository\Exceptions;

class DeleteException extends RepositoryException
{
    /**
     * @var string
     */
    protected $crud = 'repository::repository.error.delete';
}
