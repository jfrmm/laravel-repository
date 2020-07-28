<?php

namespace ASP\Repository\Base;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Abstract Class Filter
 *
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
     * The current route.
     *
     * @var Route
     */
    private $route;

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
     * The sorts to be applied
     *
     * @var array
     */
    private $sorts = [];

    /**
     * The tables already joined to base table
     *
     * @var array
     */
    protected $joined = [];

    /**
     * Getter for the Request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Getter for the Route
     *
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->route = Route::getCurrentRoute();
    }

    /**
     * Apply the filters to the builder.
     *
     * @param  Builder $builder
     *
     * @return Builder
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (!is_array($value)) {
                if ($this->hasSorts($name, $value)) {
                    $this->setSorts($name, $value);
                    continue;
                }

                if (is_null($value)) {
                    $value = '';
                }
            } else {
                if (count($value) === 0) {
                    continue;
                }
            }

            if (!$this->callFilter($name, $value)) {
                continue;
            }
        }

        foreach ($this->route->parameters as $name => $value) {
            if (!$this->callFilter($name, [$value])) {
                continue;
            }
        }

        $this->applySorts();

        return $this->builder;
    }

    /**
     * Call the object's filter method
     *
     * @param string       $name
     * @param string|array $value
     *
     * @return bool
     */
    private function callFilter(string $name, $value): bool
    {
        $name = Str::camel($name);

        if (!method_exists($this, $name)) {
            return false;
        }

        $this->$name($value);

        return true;
    }

    /**
     * Get all request filters data, trim unneeded data.
     *
     * @return array
     */
    public function filters(): array
    {
        return $this->request->except(['page', 'size', 'with']);
    }

    /**
     * Checks if we have sorts in the request
     *
     * @param string $sort
     * @param string|null $column
     *
     * @return bool
     */
    private function hasSorts(string $sort, ?string $column = null): bool
    {
        if (!is_null($column) && $sort === 'sort_by') {
            return true;
        }

        return false;
    }

    /**
     * Get the sorts from the request and store them
     *
     * @param string $sort
     * @param string|null $column
     *
     * @return void
     */
    private function setSorts(string $sort, ?string $column = null): void
    {
        foreach (explode(',', $column) as $sort) {
            if (preg_match('/.asc$/', $sort)) {
                $this->updateSortAsc(substr($sort, 0, -4));
            } elseif (preg_match('/.desc$/', $sort)) {
                $this->updateSortDesc(substr($sort, 0, -5));
            }
        }
    }

    /**
     * Sort by the given column, in ascending order
     *
     * @param string $column
     *
     * @return void
     */
    private function updateSortAsc(string $column): void
    {
        $table = with($this->builder->getModel())->getTable();

        if (env('DB_TABLE_PREFIX')) {
            $table = env('DB_TABLE_PREFIX') . $table;
        }

        if (in_array($column, $this->sortable)) {
            array_push($this->sorts, "{$table}.{$column} ASC");
        }
    }

    /**
     * Sort by the given column, in descending order
     *
     * @param string $column
     *
     * @return void
     */
    private function updateSortDesc(string $column): void
    {
        $table = with($this->builder->getModel())->getTable();

        if (env('DB_TABLE_PREFIX')) {
            $table = env('DB_TABLE_PREFIX') . $table;
        }

        if (in_array($column, $this->sortable)) {
            array_push($this->sorts, "{$table}.{$column} DESC");
        }
    }

    /**
     * Apply the sorts to the Builder
     *
     * @return void|Builder
     */
    private function applySorts()
    {
        if (count($this->sorts) > 0) {
            return $this->builder->orderByRaw(implode(', ', $this->sorts));
        }
    }

    /**
     * Wrapper for Eloquent's join, this should be used whenever you need to filter a model with various other entities
     * in a nested fashion.
     * We strongly advise using joins over whereHas when you have nested relations because:
     *  1 - The syntax is easier with join for many relations and more clear
     *  2 - Join operations are much more optimized in database engines, as opposed to `exists in <subquery>`
     *
     * This wrapper maintains the list of currently joined entities so has to not join again. After you finished
     * executing your query you should use resetFilterJoins()
     *
     * Take note that, when performing joins, you'll have issues with same name columns, so, prefix your column
     * names accordingly
     *
     * @param string $table
     * @param string $first
     * @param string $second
     *
     * @return Builder
     */
    protected function joinTables(string $table, string $first, string $second): Builder
    {
        if (in_array($table, $this->joined)) {
            return $this->builder;
        }

        $this->joined[] = $table;
        return $this->builder->join($table, $first, '=', $second);
    }

    /**
     * Reset the list of currently joined entities
     *
     * @return void
     */
    public function resetFilterJoins(): void
    {
        $this->joined = [];
    }
}
