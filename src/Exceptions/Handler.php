<?php

namespace ASP\Repository\Exceptions;

use ASP\Repository\Base\HttpStatusCode;
use Exception;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;
use Throwable;

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
     * Report or log a throwable.
     *
     * @param  \Throwable  $throwable
     *
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $throwable)
    {
        /**
         * Reports reserved for production
         */
        if (!App::environment(['local', 'preprod'])) {
            /**
             * Report Repository exceptions
             */
            if ($throwable instanceof RepositoryException) {
                $this->responder->respond($throwable);
            }

            return;
        }

        parent::report($throwable);
    }

    /**
     * Render a throwable into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable                $throwable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $throwable)
    {
        /**
         *  Handle HTTP response 401 Unauthorized
         */
        if (
            $throwable instanceof \Illuminate\Auth\AuthenticationException ||
            $throwable instanceof \Flugg\Responder\Exceptions\Http\UnauthenticatedException
        ) {
            return $this->handle401();
        }

        /**
         *  Handle other HTTP responses
         */
        if ($throwable instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            switch ($throwable->getStatusCode()) {
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
        if ($throwable instanceof RepositoryException) {
            return $this->handleRepositoryException($throwable);
        }

        /**
         * Handle with default behaviour of the framework
         */
        return parent::render($request, $throwable);
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
