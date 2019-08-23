<?php

namespace ASP\Repository\Traits;

use Exception;
use ASP\Repository\Filter;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use ASP\Repository\Exceptions\ReadException;
use Illuminate\Database\Eloquent\Collection;
use ASP\Repository\Exceptions\IndexException;
use ASP\Repository\Exceptions\CreateException;
use ASP\Repository\Exceptions\DeleteException;
use ASP\Repository\Exceptions\UpdateException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
     * @param array|null $pagination
     * @param Filter     $filters
     *
     * @return Collection|Model[]|LengthAwarePaginator|IndexException
     */
    protected static function getAllRecords(array $pagination = null, Filter $filters = null)
    {
        try {
            $builder = self::query();

            if (!empty($filters)) {
                $builder = $builder->filter($filters);
            }

            if (!empty($pagination)) {
                return $builder->paginate(
                    $pagination['size'],
                    ['*'],
                    'page',
                    $pagination['page']
                );
            }

            return $builder->get();
        } catch (Exception $e) {
            return new IndexException(null, null, $e->getMessage());
        }
    }

    /**
     * Get the specified record from the database
     *
     * @param mixed $id
     *
     * @return Model|Collection|Builder|Builder[]|ReadException
     */
    protected static function getRecordById($id)
    {
        try {
            return self::findOrFail($id);
        } catch (Exception $e) {
            return new ReadException(null, null, $e->getMessage());
        }
    }

    /**
     * Create a new record in the database
     *
     * @param Request $request
     *
     * @return Model|null|CreateException
     */
    protected static function createRecord(Request $request)
    {
        try {
            $validation = self::validateCreate($request);

            if ($validation !== true) {
                return $validation;
            } else {
                return self::create($request->all())->fresh();
            }
        } catch (Exception $e) {
            return new CreateException(null, null, $e->getMessage());
        }
    }

    /**
     * Update the specified record in the database
     *
     * @param mixed   $id
     * @param Request $request
     *
     * @return Model|null|UpdateException
     */
    protected static function updateRecordById($id, Request $request)
    {
        try {
            $validation = self::validateUpdate($request);

            if ($validation !== true) {
                return $validation;
            } else {
                return tap(
                    self::getRecordById($id),
                    function ($record) use ($request) {
                        if ($record instanceof ReadException) {
                            throw new Exception($record->getMessage());
                        }

                        $record->update($request->all());
                        $record->fresh();
                    }
                );
            }
        } catch (Exception $e) {
            return new UpdateException(null, null, $e->getMessage());
        }
    }

    /**
     * Delete the specified record from the database
     *
     * @param mixed   $id
     * @param Request $request
     *
     * @return bool|null|DeleteException
     */
    protected static function deleteRecordById($id, Request $request)
    {
        try {
            $validation = self::validateDelete($request);

            if ($validation !== true) {
                return $validation;
            } else {
                return tap(
                    self::getRecordById($id),
                    function ($record) {
                        if ($record instanceof ReadException) {
                            throw new Exception($record->getMessage());
                        }

                        $record->delete();
                    }
                );
            }
        } catch (Exception $e) {
            return new DeleteException(null, null, $e->getMessage());
        }
    }
}
