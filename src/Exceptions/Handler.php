<?php

namespace ASP\Repository\Exceptions;

use ASP\Repository\Base\HttpStatusCode;
use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * @var Responder
     */
    private $responder;

    /**
     * Create a new exception handler instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->responder = new Responder();

        parent::__construct($container);
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     *
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        /**
         * Reports reserved for production
         */
        if (!App::environment(['dev', 'preprod'])) {
            /**
             * Report Repository exceptions
             */
            if ($exception instanceof RepositoryException) {
                $this->responder->respond($exception);
            }

            return;
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception                $exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        /**
         *  Handle HTTP response 401 Unauthorized
         */
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->handle401();
        }

        /**
         *  Handle other HTTP responses
         */
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            switch ($exception->getStatusCode()) {
                case 403:
                    return $this->handle403();
                case 404:
                    return $this->handle404();
                default:
                    break;
            }
        }

        /**
         * Handle Repository exceptions
         */
        if ($exception instanceof RepositoryException) {
            return $this->handleRepositoryException($exception);
        }

        /**
         * Handle with default behaviour of the framework
         */
        return parent::render($request, $exception);
    }

    /**
     * Handle HTTP response 401 Unauthorized
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function handle401()
    {
        $status = HTTPResponse::HTTP_UNAUTHORIZED;
        $message = __('repository::api.error.unauthorized');
        $httpStatusCode = new HttpStatusCode($status, false, $message);

        return $this->responder->respond($httpStatusCode);
    }

    /**
     * Handle HTTP response 403 Forbidden
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function handle403()
    {
        $status = HTTPResponse::HTTP_FORBIDDEN;
        $message = __('repository::api.error.forbidden');
        $httpStatusCode = new HttpStatusCode($status, false, $message);

        return $this->responder->respond($httpStatusCode);
    }

    /**
     * Handle HTTP response 404 Not Found
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function handle404()
    {
        $status = HTTPResponse::HTTP_NOT_FOUND;
        $message = __('repository::api.error.not_found');
        $httpStatusCode = new HttpStatusCode($status, false, $message);

        return $this->responder->respond($httpStatusCode);
    }

    /**
     * Handle Repository exceptions
     *
     * @param RepositoryException $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleRepositoryException(RepositoryException $exception)
    {
        return $this->responder->respond($exception);
    }
}
