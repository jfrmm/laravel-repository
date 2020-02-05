<?php

namespace ASP\Repository\Base;

use ASP\Repository\Traits\HasPagination;
use ASP\Repository\Traits\MakesResponses;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

/**
 * Class Controller
 *
 * @package ASP\Repository\Base
 */
class Controller extends BaseController
{
    use HasPagination, MakesResponses;

    /**
     * @var Illuminate\Http\Request
     */
    public $request;

    /**
     * @var HTTPResponse
     */
    public $httpResponses;

    /**
     * @return Illuminate\Http\Request|void
     */
    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            $this->request = $request;
            $this->createPagination($request);
            return $next($request);
        })->only('index');

        $this->httpResponses = new HTTPResponse();
    }
}
