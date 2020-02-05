<?php

namespace ASP\Repository\Traits;

use ASP\Repository\Base\HttpStatusCode;
use ASP\Repository\Base\Model as BaseModel;
use ASP\Repository\Exceptions\RepositoryException;
use ASP\Repository\Response;
use ASP\Repository\Serializers\ErrorSerializer;
use Flugg\Responder\Http\Responses\ErrorResponseBuilder;
use Flugg\Responder\Http\Responses\SuccessResponseBuilder;
use Flugg\Responder\Transformers\Transformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

/**
 * @package ASP\Repository\Traits
 */
trait MakesResponses
{
    use \Flugg\Responder\Http\MakesResponses {
        success as private makesResponsesSuccess;
        error as private makesResponsesError;
    }

    /**
     * @var SuccessResponseBuilder|ErrorResponseBuilder
     */
    private $response;

    /**
     * @var int
     */
    private $status;

    /**
     * Generate the response
     *
     * @param Model|Builder|RepositoryException|HttpStatusCode $data
     * @param Transformer                                      $transformer
     * @param string                                           $action
     * @param BaseModel|Model|string                           $model
     *
     * @return JsonResponse
     *
     * @throws ReflectionException
     */
    public function respond($data, $transformer = null, $action = null, $model = null)
    {
        if ($data instanceof RepositoryException) {
            $this->prepareRepositoryExceptionResponse($data);
        } elseif ($data instanceof HttpStatusCode) {
            $this->prepareHTTPStatusResponse($data);
        } else {
            $this->prepareResponse($data, $transformer, $action, $model);
        }

        return $this->response->respond($this->status);
    }

    /**
     * Generate the response, but with fewer options
     *
     * @param int        $status
     * @param string     $message
     * @param array|null $data
     *
     * @return JsonResponse
     */
    public function simplyRespond($status, $message = '', $data = null)
    {
        if (is_null($status)) {
            $status = 200;
        }

        $this->status = $status;

        if ($this->status < 400) {
            $this->prepareSimpleSuccessResponse($message, $data);
        } else {
            $this->prepareSimpleErrorResponse($message);
        }

        return $this->response->respond($this->status);
    }

    /**
     * Prepare a response
     *
     * @param Model|Collection|RepositoryException|LengthAwarePaginator $data
     * @param Transformer                                               $transformer
     * @param string                                                    $action
     * @param BaseModel|Model|string                                    $model
     *
     * @return void
     *
     * @throws ReflectionException
     */
    private function prepareResponse($data, $transformer, $action, $model)
    {
        if ($model instanceof BaseModel) {
            $modelName = $model->getModelName();
        } elseif (is_string($model)) {
            $modelName = $model;
        } else {
            $modelName = 'Model';
        }

        switch ($action) {
            case 'index':
                $this->status = HTTPResponse::HTTP_OK;
                $message = __('repository::repository.success.index', ['entity' => $modelName]);
                break;
            case 'store':
                $this->status = HTTPResponse::HTTP_CREATED;
                $message = __('repository::repository.success.create', ['entity' => $modelName]);
                break;
            case 'show':
                $this->status = HTTPResponse::HTTP_OK;
                $message = __('repository::repository.success.read', ['entity' => $modelName]);
                break;
            case 'update':
                $this->status = HTTPResponse::HTTP_OK;
                $message = __('repository::repository.success.update', ['entity' => $modelName]);
                break;
            case 'destroy':
                $this->status = HTTPResponse::HTTP_OK;
                $message = __('repository::repository.success.delete', ['entity' => $modelName]);
                break;
            default:
                $this->status = null;
                $message = null;
        }

        if ($data instanceof Model || $data instanceof Collection) {
            $this->success($data, $transformer);
            $this->withMessage($message);
        } elseif ($data instanceof LengthAwarePaginator) {
            $metadata = $this->getPaginationProperties($data);
            $this->success($data->items(), $transformer);
            $this->withPagination($metadata, $message);
        } else {
            $this->success($data);
        }
    }

    /**
     * Prepare a response with a RepositoryException
     *
     * @param RepositoryException $exception
     *
     * @return void
     *
     * @throws ReflectionException
     */
    private function prepareRepositoryExceptionResponse(RepositoryException $exception)
    {
        $reflect = new ReflectionClass($exception);

        switch ($reflect->getShortName()) {
            case 'ValidationException':
                $this->status = HTTPResponse::HTTP_UNPROCESSABLE_ENTITY;
                $message = $exception->getMessage();
                $errors = $exception->getExceptionData();
                $data = [];
                foreach ($errors as $key => $value) {
                    foreach (Arr::wrap($value) as $error) {
                        $data[] = [$key => $error];
                    }
                }
                break;

            case 'ReadException':
                $this->status = HTTPResponse::HTTP_NOT_FOUND;
                $message = $exception->getMessage();
                $data = $exception->getExceptionData();
                break;

            case 'IndexException':
            case 'CreateException':
            case 'UpdateException':
            case 'DeleteException':
                $this->status = HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;
                $message = $exception->getMessage();
                $data = $exception->getExceptionData();
                break;

            default:
                $this->status = HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;
                $message = $exception->getMessage();
                $data = null;
        }

        $this->error($message, $data);
    }

    /**
     * Prepare a response with a specific HTTP status code
     *
     * @param HttpStatusCode $httpStatusCode
     *
     * @return void
     */
    private function prepareHTTPStatusResponse(HttpStatusCode $httpStatusCode)
    {
        $this->status = $httpStatusCode->status;
        $this->data = null;

        $this->error($httpStatusCode->message);
    }

    /**
     * Prepare a simple success response, with data and a message
     *
     * @param string $message
     * @param array  $data
     *
     * @return void
     */
    private function prepareSimpleSuccessResponse($message, $data)
    {
        $this->success($data);
        $this->withMessage($message);
    }


    /**
     * Prepare a simple error response, with a message
     *
     * @param string $message
     *
     * @return void
     */
    private function prepareSimpleErrorResponse($message)
    {
        $this->data = null;
        $this->error($message);
    }

    /**
     * Specify the message
     *
     * @param string|null $message
     *
     * @return MakesResponses
     */
    private function withMessage(?string $message = null)
    {
        if (!is_null($message) && $message !== '') {
            $this->response = $this->response->meta(['message' => $message]);
        }

        return $this;
    }

    /**
     * Add pagination data
     *
     * @param array|null  $paginationData
     * @param string|null $message
     *
     * @return MakesResponses
     */
    private function withPagination(?array $paginationData = null, ?string $message = null)
    {
        $meta = ['pagination' => $paginationData];

        if (!is_null($message)) {
            $meta['message'] = $message;
        }

        $this->response = $this->response->meta($meta);

        return $this;
    }

    /**
     * Override success method from Responder
     *
     * @param mixed            $data        The data to send to the frontend
     * @param Transformer|null $transformer The class that transforms the data
     *
     * @return MakesResponses
     */
    private function success($data = null, $transformer = null)
    {
        $this->status = $this->status ?? HTTPResponse::HTTP_OK;
        $this->response = $this->makesResponsesSuccess($data, $transformer, null);

        return $this;
    }

    /**
     * Override error method from Responder
     *
     * @param string|null $message
     * @param array|null  $errors
     * @param bool        $dismissible
     *
     * @return MakesResponses
     */
    private function error(?string $message = null, ?array $errors = null, $dismissible = false)
    {
        $this->status = $this->status ?? HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;

        if ($dismissible) {
            if (is_null($errors)) {
                $errors = [Response::DISMISSIBLE => $dismissible];
            } else {
                $errors[Response::DISMISSIBLE] = $dismissible;
            }
        }

        /**
         * $errors contains validation errors, which we'll output
         * in a more convenient format
         */
        if (is_null($errors)) {
            $this->response = $this->makesResponsesError($this->status, $message)
                ->serializer(ErrorSerializer::class);
        } else {
            $this->response = $this->makesResponsesError($this->status, $message)
                ->data($errors)
                ->serializer(ErrorSerializer::class);
        }

        return $this;
    }
}
