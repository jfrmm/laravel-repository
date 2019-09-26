<?php

namespace ASP\Repository\Exceptions;

/**
 * @package ASP\Repository\Exceptions
 */
class IndexException extends RepositoryException
{
    /**
     * @var string
     */
    protected $crud = 'repository::repository.error.index';
}
