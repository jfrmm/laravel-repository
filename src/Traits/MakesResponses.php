<?php

namespace ASP\Repository\Traits;

use ReflectionClass;
use ASP\Repository\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Flugg\Responder\Transformers\Transformer;
use ASP\Repository\Serializers\ErrorSerializer;
use ASP\Repository\Exceptions\RepositoryException;
use Flugg\Responder\Http\Responses\ErrorResponseBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Flugg\Responder\Http\Responses\SuccessResponseBuilder;
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
     * @var
     */
    private $status;

    /**
     * @var array
     */
    private $responses = [];

    /**
     * Generate the response
     *
     * @param Model|Builder|RepositoryException       $data
     * @param Transformer                             $transformer
     * @param string                                  $action
     *
     * @return JsonResponse
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
     * @param Model|Builder|RepositoryException|LengthAwarePaginator $data
     * @param Transformer                                            $transformer
     * @param string                                                 $action
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

        if ($data instanceof Model || is_null($data)) {
            $this->success($data, $transformer);
        } elseif ($data instanceof LengthAwarePaginator) {
            // get paginator
        } elseif ($data instanceof Builder) {
            $this->success($data->get(), $transformer);
        }

        $this->withMessage($message);
    }

    /**
     * @param RepositoryException $exception
     *
     * @return void
     */
    private function prepareRepositoryExceptionResponse($exception)
    {
        $reflect = new ReflectionClass($exception);

        switch ($reflect->getShortName()) {
            case 'ValidationException':
                $this->status = HTTPResponse::HTTP_UNPROCESSABLE_ENTITY;
                $this->error($exception->getMessage(), $exception->getValidationErrors());
                break;
            default:
                $this->status = HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;
                $this->error($exception->getMessage(), null);
        }
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
     * Override success method from Responder
     *
     * @param mixed            $data        The data to send to the frontend
     * @param Transformer|null $transformer The class that transforms the data
     *
     * @return MakesResponses
     */
    private function success($data = null, $transformer = null)
    {
        $this->response = $this->makesResponsesSuccess($data, $transformer, null);
        $this->status = $this->status ?? HTTPResponse::HTTP_OK;

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
            if (empty($data)) {
                $data = [Response::DISMISSIBLE => $dismissible];
            } else {
                $data[Response::DISMISSIBLE] = $dismissible;
            }
        }

        $this->response = $this->makesResponsesError($this->status, $message)
                            ->data($errors)
                            ->serializer(ErrorSerializer::class);

        return $this;
    }
}
