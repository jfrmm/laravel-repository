<?php

namespace ASP\Repository\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasPagination
{

    /**
     * Array of pagination properties for the model
     *
     * @var LengthAwarePaginator|null
     */
    protected $paginator = null;


    /**
     * @return array
     */
    public function getPaginationProperties()
    {
        if (empty($this->paginator)) {
            return null;
        }

        $data = [
            'current_page' => (int)$this->paginator->currentPage(),
            'page_size' => (int)$this->paginator->perPage(),
            'last_page' => (bool)($this->paginator->currentPage() >= $this->paginator->lastPage()),
            'total' => (int)$this->paginator->total()
        ];
        $this->paginator = null;
        return $data;
    }

    public function processPagination(Builder $query): void
    {
        $this->paginator = $query->paginate(
            $this->pagination['size'],
            ['*'],
            'page',
            $this->pagination['page']
        );
    }
}
