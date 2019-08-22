<?php

namespace ASP\Repository\Exceptions;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use ASP\Repository\Traits\MakesResponses;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

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
        $this->status = $status ?? HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;
        $this->message = $message ?? HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;
        $this->data = $data;
        $this->dismissible = $dismissible;

        if (!empty($this->model)) {
            $entity = __('crud.entities.' . Str::snake(class_basename($this->model)));
            $this->message = $message ?? __($this->crud, ['entity' => $entity]);
        }
    }

    /**
     * Report the Exception to the log
     *
     * @return void
     */
    public function report()
    {
        Log::error($this->message);
    }
}
