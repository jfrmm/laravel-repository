<?php

namespace ASP\Repository\Base;

use ASP\Repository\Traits\HasPagination;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use HasPagination;

    public $request;

    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            $this->request = $request;
            $this->createPagination($request);
            return $next($request);
        })->only('index');
    }

}