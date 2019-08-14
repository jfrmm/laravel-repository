<?php

namespace App\Repository\Traits;

use App\Repository\ModelFilters;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait Repository
{
    use ModelFilter;

    /**
     * Return all the records in the database
     *
     * @param ModelFilters $filters
     *
     * @return Collection|Model[]
     * @throws Exception
     */
    protected static function getAllRecords(ModelFilters $filters = null)
    {
        try {
            if (!empty($filters)) {
                return self::filter($filters)->get();
            }

            return self::all();
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
     * Get the specified record from the database
     *
     * @param mixed $id
     *
     * @return Model|Collection|Builder|Builder[]
     * @throws Exception
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
