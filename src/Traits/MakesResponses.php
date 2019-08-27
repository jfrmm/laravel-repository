<?php

namespace ASP\Repository\Traits;

use ReflectionClass;
use ASP\Repository\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Flugg\Responder\Transformers\Transformer;
use ASP\Repository\Serializers\ErrorSerializer;
use Illuminate\Pagination\LengthAwarePaginator;
use ASP\Repository\Exceptions\RepositoryException;
use Flugg\Responder\Http\Responses\ErrorResponseBuilder;
use Flugg\Responder\Http\Responses\SuccessResponseBuilder;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

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
     * @var integer
     */
    private $status;

    /**
     * Generate the response
     *
     * @param Model|Builder|RepositoryException $data
     * @param Transformer                       $transformer
     * @param string                            $action
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function respond($data, $transformer, $action)
    {
        if ($data instanceof RepositoryException) {
            $this->prepareRepositoryExceptionResponse($data);
        } else {
            $this->prepareResponse($data, $transformer, $action);
        }

        return $this->response->respond($this->status);
    }

    /**
     * Prepare a response
     *
     * @param Model|Collection|RepositoryException|LengthAwarePaginator $data
     * @param Transformer                                               $transformer
     * @param string                                                    $action
     *
     * @return void
     */
    private function prepareResponse($data, $transformer, $action)
    {
        switch ($action) {
            case 'index':
                $this->status = HTTPResponse::HTTP_OK;
                $message = 'Indexed';
                break;
            case 'store':
                $this->status = HTTPResponse::HTTP_CREATED;
                $message = 'Created';
                break;
            case 'show':
                $this->status = HTTPResponse::HTTP_OK;
                $message = 'Shown';
                break;
            case 'update':
                $this->status = HTTPResponse::HTTP_OK;
                $message = 'Updated';
                break;
            case 'destroy':
                $this->status = HTTPResponse::HTTP_ACCEPTED;
                $message = 'Deleted';
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
            $this->withPagination($metadata);
        }
    }

    /**
     * Prepare a response with a RepositoryException
     *
     * @param RepositoryException $exception
     *
     * @return void
     * @throws ReflectionException
     */
    private function prepareRepositoryExceptionResponse(RepositoryException $exception)
    {
        $reflect = new ReflectionClass($exception);

        switch ($reflect->getShortName()) {
            case 'ValidationException':
                $this->status = HTTPResponse::HTTP_UNPROCESSABLE_ENTITY;
                $message = $exception->getMessage();
                $data = $exception->getExceptionData();
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
     * Specify the message
     *
     * @param string|null $message
     *
     * @return MakesResponses
     */
    private function withMessage(string $message = null)
    {
        $this->response = $this->response->meta(['message' => $message]);

        return $this;
    }

    /**
     * Add pagination data
     *
     * @param array|null $paginationData
     *
     * @return MakesResponses
     */
    private function withPagination(array $paginationData = null)
    {
        $this->response = $this->response->meta(['pagination' => $paginationData]);

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
    private function error(string $message = null, $errors = null, $dismissible = false)
    {
        $this->status = $this->status ?? HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;

        if ($dismissible) {
            if (empty($errors)) {
                $errors = [Response::DISMISSIBLE => $dismissible];
            } else {
                $errors[Response::DISMISSIBLE] = $dismissible;
            }
        }

        $this->response = $this->makesResponsesError($this->status, $message)
                            ->data($errors)
                            ->serializer(ErrorSerializer::class);

        return $this;
    }
}
