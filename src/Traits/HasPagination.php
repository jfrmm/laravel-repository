<?php

namespace ASP\Repository\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * @package ASP\Repository\Traits
 */
trait HasPagination
{
    /**
     * Array of pagination properties for the model
     *
     * @var array
     */
    protected $pagination = [];

    /**
     * Array of pagination properties for the model
     *
     * @var LengthAwarePaginator|null
     */
    protected $paginator = null;

    /**
     * Set the pagination properties
     *
     * @param Request $request
     *
     * @return void
     */
    public function createPagination(Request $request)
    {
        foreach ($request->only(['page', 'size']) as $name => $value) {
            if (empty($name)) {
                continue;
            }
            $name = Str::camel($name);
            $this->pagination[$name] = $value;
        }
    }

    /**
     * Get the pagination metadata
     *
     * @param LengthAwarePaginator $paginator
     *
     * @return array|null
     */
    public function getPaginationProperties(LengthAwarePaginator $paginator)
    {
        if (empty($paginator)) {
            return null;
        }

        return [
            'current_page' => (int) $paginator->currentPage(),
            'page_size' => (int) $paginator->perPage(),
            'last_page' => (bool) ($paginator->currentPage() >= $paginator->lastPage()),
            'total' => (int) $paginator->total(),
        ];
    }
}
