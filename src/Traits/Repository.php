<?php

namespace ASP\Repository\Traits;

use ASP\Repository\Filter;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait Repository
{
    /**
     * Filter a result set.
     *
     * @param  Builder $query
     * @param  Filter  $filters
     *
     * @return Builder
     */
    public function scopeFilter(Builder $query, Filter $filters)
    {
        return $filters->apply($query);
    }

    /**
     * Return all the records in the database
     *
     * @param Filter     $filters
     * @param array|null $pagination
     *
     * @return Collection|Model[]|LengthAwarePaginator
     * @throws Exception
     */
    protected static function getAllRecords(Filter $filters = null, array $pagination = null)
    {
        try {
            $builder = self::query();

            if (!empty($filters)) {
                $builder = $builder->filter($filters);
            }

            if (!empty($pagination)) {
                $builder = $builder->paginate(
                    $pagination['size'],
                    ['*'],
                    'page',
                    $pagination['page']
                );
            }

            return $builder;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the specified record from the database
     *
     * @param mixed $id
     *
     * @return Model|Collection|Builder|Builder[]
     */
    protected static function getRecordById($id)
    {
        try {
            return self::findOrFail($id);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a new record in the database
     *
     * @param Request $request
     *
     * @return Model|null
     * @throws Exception
     */
    protected static function createRecord(Request $request)
    {
        try {
            $validation = self::validateCreate($request);

            if ($validation === true) {
                return self::create($request->all())->fresh();
            }

            return $validation;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update the specified record in the database
     *
     * @param mixed   $id
     * @param Request $request
     *
     * @return Model|null
     * @throws Exception
     */
    protected static function updateRecordById($id, Request $request)
    {
        try {
            $validation = self::validateUpdate($request);

            if ($validation === true) {
                return tap(
                    self::getRecordById($id),
                    function ($record) use ($request) {
                        return $record->update($request->all());
                    }
                )->fresh();
            }

            return $validation;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete the specified record from the database
     *
     * @param mixed   $id
     *
     * @param Request $request
     *
     * @return bool|null
     * @throws Exception
     */
    protected static function deleteRecordById($id, Request $request)
    {
        try {
            $validation = self::validateDelete($request);

            if ($validation === true) {
                return self::getRecordById($id)->delete();
            }

            return $validation;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
