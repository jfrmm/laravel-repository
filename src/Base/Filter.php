<?php

namespace ASP\Repository\Base;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

/**
 * Based on Jeffrey Way's Laracast's dedicated Query String Filtering,
 * aims at being a simple way to create dedicated filters per model.
 *
 * We can make use of https://laravel.com/docs/5.8/eloquent#global-scopes
 * to apply an Eloquent Global Scope to a Model, and thus, apply any
 * query params as filtering values.
 *
 * This functionality is used by ASP\Repository\Traits\Repository.
 *
 * @link https://laracasts.com/series/eloquent-techniques/episodes/4
 * @link https://github.com/laracasts/Dedicated-Query-String-Filtering
 *
 * @package ASP\Repository\Base
 */
abstract class Filter
{
    /**
     * The request object.
     *
     * @var Request
     */
    private $request;

    /**
     * The builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * The fields by which we can sort the model
     *
     * @var array
     */
    protected $sortable = [];

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the filters to the builder.
     *
     * @param  Builder $builder
     *
     * @return Builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            $name = Str::camel($name);

            if ($name === 'sortAsc') {
                $this->sortAsc($value);
                continue;
            }

            if ($name === 'sortDesc') {
                $this->sortDesc($value);
                continue;
            }

            if (! method_exists($this, $name)) {
                continue;
            }

            if (strlen($value)) {
                $this->$name($value);
            }
        }

        return $this->builder;
    }

    /**
     * Get all request filters data, trim unneeded data.
     *
     * @return array
     */
    public function filters()
    {
        return $this->request->except(['page', 'size', 'with']);
    }

    /**
     * Sort by the given field, in ascending order
     *
     * @param string $field
     *
     * @return Builder
     */
    private function sortAsc(string $field)
    {
        if (in_array($field, $this->sortable)) {
            return $this->builder->orderBy($field, 'asc');
        }
    }

    /**
     * Sort by the given field, in descending order
     *
     * @param string $field
     *
     * @return Builder
     */
    private function sortDesc(string $field)
    {
        if (in_array($field, $this->sortable)) {
            return $this->builder->orderBy($field, 'desc');
        }
    }
}
