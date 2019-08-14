<?php

namespace ASP\Repository\Traits;

use ASP\Repository\Response;
use ASP\Repository\Serializers\ErrorSerializer;
use ASP\Repository\Serializers\MultiCodeSerializer;
use Flugg\Responder\Http\Responses\ErrorResponseBuilder;
use Flugg\Responder\Http\Responses\SuccessResponseBuilder;
use Flugg\Responder\Transformers\Transformer;
use Illuminate\Http\JsonResponse;
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
    public $response;
    /**
     * @var
     */
    public $status;
    /**
     * @var array
     */
    public $responses = [];

    /**
     * Override success method from Responder
     *
     * @param null             $status
     * @param null             $data        The data to send to the frontend
     * @param null|Transformer $transformer The class that transforms the data
     *
     * @return MakesResponses
     */
    public function success(
        $data = null,
        $transformer = null,
        $status = null
    ) {
        $this->response = $this->makesResponsesSuccess($data, $transformer, null);
        $this->status = $status ?? HTTPResponse::HTTP_OK;

        return $this;
    }

    /**
     * Override error method from Responder
     *
     * @param null        $status
     * @param string|null $message
     * @param null        $data
     * @param bool        $dismissible
     *
     * @return MakesResponses
     */
    public function error($status = null, string $message = null, $data = null, $dismissible = false)
    {
        $this->status = $status ?? HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;
        if ($dismissible) {
            if (empty($data)) {
                $data = [Response::DISMISSIBLE => $dismissible];
            } else {
                $data[Response::DISMISSIBLE] = $dismissible;
            }
        }
        $this->response = $this->makesResponsesError($status, $message)
            ->data($data)
            ->serializer(ErrorSerializer::class);
        return $this;
    }

    /**
     * Override error method from Responder
     *
     * @param null $data
     *
     * @return MakesResponses
     */
    public function multiCode($data = null)
    {
        $this->status = HTTPResponse::HTTP_MULTI_STATUS;
        $this->response = $this->makesResponsesSuccess($data)
            ->serializer(MultiCodeSerializer::class);

        return $this;
    }

    /**
     * Specify relation to be loaded
     *
     * @param array $relations
     *
     * @return MakesResponses
     */
    public function withRelations($relations = [])
    {
        $this->response = $this->response->with($relations);
        return $this;
    }

    /**
     * Specify the message
     *
     * @param string|null $message
     *
     * @return MakesResponses
     */
    public function withMessage(string $message = null)
    {
        $this->response = $this->response->meta([Response::MESSAGE => $message]);
        return $this;
    }

    /**
     * Add pagination data
     *
     * @param null $paginationData
     *
     * @return MakesResponses
     */
    public function withPagination($paginationData = null)
    {
        $this->response = $this->response->meta([Response::PAGINATION => $paginationData]);
        return $this;
    }

    /**
     * Specify the message
     *
     * @param array $errors
     *
     * @return MakesResponses
     */
    public function withErrors(array $errors = [])
    {
        $this->response = $this->response->meta([Response::ERRORS => $errors]);
        return $this;
    }

    /**
     * Generate the response
     *
     * @param null $status
     *
     * @return JsonResponse
     */
    public function respond($status = null)
    {
        $status = $status ?? $this->status;
        return $this->response->respond($status);
    }
}
