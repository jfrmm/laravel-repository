<?php

namespace ASP\Repository\Exceptions;

class DeleteException extends RepositoryException
{
    /**
     * @var string
     */
    protected $crud = 'crud.error.delete';
}
