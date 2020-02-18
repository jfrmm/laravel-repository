<?php

namespace ASP\Repository\Traits;

use ASP\Repository\Base\Filter;
use ASP\Repository\Exceptions\CreateException;
use ASP\Repository\Exceptions\DeleteException;
use ASP\Repository\Exceptions\IndexException;
use ASP\Repository\Exceptions\ReadException;
use ASP\Repository\Exceptions\UpdateException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
     * @return Collection|Model|LengthAwarePaginator
     *
     * @throws IndexException
     */
    final protected static function getAllRecords(?array $pagination = null, ?Filter $filters = null)
    {
        try {
            return self::commitGetAllRecords($pagination, $filters);
        } catch (\Exception $exception) {
            return new IndexException(null, null, $exception->getMessage());
        }
    }

    /**
     * Get the specified record from the database.
     *
     * @param mixed $id
     *
     * @return Model|Collection|Builder
     *
     * @throws ReadException
     */
    final protected static function getRecordById($id)
    {
        try {
            return self::commitGetRecordById($id);
        } catch (\Exception $exception) {
            return new ReadException(null, null, $exception->getMessage());
        }
    }

    /**
     * Create a new record in the database.
     *
     * @param Request $request
     *
     * @return Model|null
     *
     * @throws CreateException
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
     * Update the specified record in the database.
     *
     * @param mixed   $id
     * @param Request $request
     *
     * @return Model|null
     *
     * @throws UpdateException
     */
    final protected static function updateRecordById($id, Request $request)
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
     * Delete the specified record from the database.
     *
     * @param mixed   $id
     * @param Request $request
     *
     * @return bool|null
     *
     * @throws DeleteException
     */
    final protected static function deleteRecordById($id, Request $request)
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
     * Commit to get all records in the database.
     * Optionally may be paginated and filtered.
     * Override this method when you need to add business logic.
     *
     * @param array  $pagination
     * @param Filter $filters
     *
     * @return Collection|Model|LengthAwarePaginator
     */
    private static function commitGetAllRecords(array $pagination = null, Filter $filters = null)
    {
        $builder = self::query();

        if (!is_null($filters)) {
            $builder = $builder->filter($filters);
        }

        if (!is_null($pagination)) {
            $size = array_key_exists('size', $pagination) ? $pagination['size'] : 10;

            return $builder->paginate(
                $size,
                ['*'],
                'page',
                $pagination['page']
            );
        }

        $table = with($builder->getModel())->getTable();

        return $builder->distinct()->get(["{$table}.*"]);
    }

    /**
     * Commit to get a record in the database.
     * Override this method when you need to add business logic.
     *
     * @param mixed $id
     *
     * @return Model
     *
     * @throws \Exception
     */
    private function commitGetRecordById($id)
    {
        return self::findOrFail($id);
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
