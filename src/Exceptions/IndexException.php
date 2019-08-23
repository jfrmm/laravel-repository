<?php

namespace ASP\Repository\Exceptions;

class IndexException extends RepositoryException
{
    /**
     * @var string
     */
    protected $crud = 'crud.error.index';
}
