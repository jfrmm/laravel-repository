<?php

namespace App\Repository\Traits;

use App\Repository\ModelFilters;
use Illuminate\Database\Eloquent\Builder;

/**
 * Based on Jeffrey Way's Laracast's dedicated Query String Filtering,
 * aims at being a simple way to create dedicated filters per model
 *
 * @link https://laracasts.com/series/eloquent-techniques/episodes/4
 * @link https://github.com/laracasts/Dedicated-Query-String-Filtering
 */
trait ModelFilter
{
    /**
     * Filter a result set.
     *
     * @param  Builder      $query
     * @param  ModelFilters $filters
     *
     * @return Builder
     */
    public function scopeFilter(Builder $query, ModelFilters $filters)
    {
        return $filters->apply($query);
    }
}
