<?php

namespace ASP\Repository\Exceptions;

use ASP\Repository\Traits\MakesResponses;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RepositoryException extends Exception
{
    use MakesResponses;

    /**
     * Model for the exception
     *
     * @var Model
     */
    protected $model;

    /**
     * HTTP status code for the exception
     *
     * @var int
     */
    protected $status;

    /**
     * Message for the exception
     *
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $crud;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var bool
     */
    private $dismissible;

    /**
     * Constructor.
     *
     * @param Model   $model
     * @param integer $status
     * @param string  $message
     * @param null    $data
     * @param bool    $dismissible
     */
    public function __construct(
        Model $model = null,
        int $status = null,
        string $message = null,
        $data = null,
        $dismissible = false
    ) {
        parent::__construct();

        $this->model = $model;
        $this->status = $status ?? config('api.status.internal_server_error');
        $this->message = $message ?? __('api.error.internal_server_error');
        $this->data = $data;
        $this->dismissible = $dismissible;

        if (!empty($this->model)) {
            $entity = __('crud.entities.' . Str::snake(class_basename($this->model)));
            $this->message = $message ?? __($this->crud, ['entity' => $entity]);
        }
    }
}
