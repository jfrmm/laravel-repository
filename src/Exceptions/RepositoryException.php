<?php

namespace ASP\Repository\Exceptions;

use ASP\Repository\Base\Model;
use ASP\Repository\Traits\MakesResponses;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

/**
 * @package ASP\Repository\Exceptions
 */
class RepositoryException extends Exception
{
    use MakesResponses;

    /**
     * This class short name
     *
     * @var string
     */
    private $classShortName;

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
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    protected $dismissible;

    /**
     * @var string
     */
    protected $crud;

    /**
     * Constructor.
     *
     * @param Model|null      $model
     * @param int|null        $status
     * @param string|null     $message
     * @param array|null      $data
     * @param bool            $dismissible
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public function __construct(
        ?Model $model = null,
        ?int $status = null,
        ?string $message = null,
        ?array $data = null,
        $dismissible = false
    ) {
        parent::__construct();

        $reflect = new ReflectionClass($this);
        $this->classShortName = $reflect->getShortName();

        $this->model = $model;
        $this->status = $status ?? HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;
        $this->message = $message ?? HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;
        $this->data = $data ?? $this->setExceptionData();
        $this->dismissible = $dismissible;

        if (!$this->model) {
            $modelName = $model instanceof Model ? $model->getModelName() : 'Model';
            $this->message = $message ?? __($this->crud, ['entity' => $modelName]);
        }
    }

    /**
     * Get the exception data
     *
     * @return array
     */
    public function getExceptionData()
    {
        return $this->data;
    }

    /**
     * Get the exception status
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Report the exception to the log
     *
     * @return void
     */
    public function report()
    {
        $this->logException();
    }

    /**
     * Log this exception in a readable manner
     *
     * @return void
     */
    private function logException()
    {
        switch ($this->classShortName) {
            case 'IndexException':
            case 'CreateException':
            case 'ReadException':
            case 'UpdateException':
            case 'DeleteException':
                Log::error($this->message);
                Log::error(print_r($this->getExceptionData(), true));
                break;
            default:
                Log::error($this->message);
        }
    }

    /**
     * Set the exception data
     *
     * @return array
     */
    private function setExceptionData()
    {
        $data = [
            'exception' => $this->classShortName,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];

        if (!App::environment('prod')) {
            $data['trace'] = $this->getTraceAsString();
        }

        return $data;
    }
}
