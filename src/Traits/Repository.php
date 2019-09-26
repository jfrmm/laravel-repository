<?php

namespace ASP\Repository\Traits;

use ASP\Repository\Base\Filter;
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

/**
 * @package ASP\Repository\Traits
 */
trait Repository
{
    /**
     * Filter a result set.
     *
     * Please refer to ASP\Repository\Base\Filter for more info.
     *
     * @param Builder $query
     * @param Filter  $filters
     *
     * @return Builder
     */
    public function scopeFilter(Builder $query, Filter $filters)
    {
        return $filters->apply($query);
    }

    /**
     * Return all the records in the database.
     *
     * @param array|null    $pagination
     * @param Filter|null   $filters
     *
     * @return Collection|Model|LengthAwarePaginator|IndexException
     */
    protected static function getAllRecords(array $pagination = null, Filter $filters = null)
    {
        try {
            $builder = self::query();

            if (! is_null($filters)) {
                $builder = $builder->filter($filters);
            }

            if (! is_null($pagination)) {
                return $builder->paginate(
                    $pagination['size'],
                    ['*'],
                    'page',
                    $pagination['page']
                );
            }

            return $builder->get();
        } catch (\Exception $exception) {
            return new IndexException(null, null, $exception->getMessage());
        }
    }

    /**
     * Get the specified record from the database.
     *
     * @param mixed $id
     *
     * @return Model|Collection|Builder|ReadException
     */
    protected static function getRecordById($id)
    {
        try {
            return self::findOrFail($id);
        } catch (\Exception $exception) {
            return new ReadException(null, null, $exception->getMessage());
        }
    }

    /**
     * Create a new record in the database.
     *
     * @param Request $request
     *
     * @return Model|CreateException|null
     */
    final protected static function createRecord(Request $request)
    {
        try {
            $validation = self::validateCreate($request);

            if ($validation !== true) {
                return $validation;
            }

            return self::commitCreateRecord($request);
        } catch (\Exception $exception) {
            return new CreateException(null, null, $exception->getMessage());
        }
    }

    /**
     * Commit to create a new record in the database.
     * Override this method when you need to add business logic.
     *
     * @param Request $request
     *
     * @return Model|null
     *
     * @throws \Exception
     */
    private static function commitCreateRecord(Request $request)
    {
        return self::create($request->all())->fresh();
    }

    /**
     * Update the specified record in the database.
     *
     * @param mixed   $id
     * @param Request $request
     *
     * @return Model|UpdateException|null
     */
    protected static function updateRecordById($id, Request $request)
    {
        try {
            $validation = self::validateUpdate($request);

            if ($validation !== true) {
                return $validation;
            }

            return self::commitUpdateRecordById($id, $request);
        } catch (\Exception $exception) {
            return new UpdateException(null, null, $exception->getMessage());
        }
    }

    /**
     * Commit to update the specified record in the database.
     * Override this method when you need to add business logic.
     *
     * @param mixed   $id
     * @param Request $request
     *
     * @return Model|null
     *
     * @throws \Exception
     */
    private static function commitUpdateRecordById($id, Request $request)
    {
        $record = self::getRecordById($id);

        if ($record instanceof ReadException) {
            throw new \Exception($record->getMessage());
        }

        $record->update($request->all());

        return $record->fresh();
    }

    /**
     * Delete the specified record from the database.
     *
     * @param mixed   $id
     * @param Request $request
     *
     * @return bool|DeleteException|null
     */
    protected static function deleteRecordById($id, Request $request)
    {
        try {
            $validation = self::validateDelete($request);

            if ($validation !== true) {
                return $validation;
            }

            return self::commitDeleteRecordById($id, $request);
        } catch (\Exception $exception) {
            return new DeleteException(null, null, $exception->getMessage());
        }
    }

    /**
     * Commit to delete the specified record from the database.
     * Override this method when you need to add business logic.
     *
     * @param mixed $id
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    private static function commitDeleteRecordById($id)
    {
        /**
         * Read more about tap()
         *
         * @link https://medium.com/@taylorotwell/tap-tap-tap-1fc6fc1f93a6
         */
        return tap(
            self::getRecordById($id),
            static function ($record) {
                if ($record instanceof ReadException) {
                    throw new \Exception($record->getMessage());
                }

                $record->delete();
            }
        );
    }
}
