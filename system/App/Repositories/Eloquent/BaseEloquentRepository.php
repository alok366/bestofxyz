<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Capsule\Manager;
use App\Contracts\Repositories\WriteRepositoryInterface;
use App\Contracts\Repositories\ReadRepositoryInterface;

abstract class BaseEloquentRepository implements ReadRepositoryInterface, WriteRepositoryInterface
{
    protected $model;

    public function all(array $filters = [], array $columns = ['*'])
    {
        $query = $this->model->query();
        foreach ($filters as $field => $value) :
            if (!is_null($value)) :
                $query->where($field, $value);
            endif;
        endforeach;

        // Select specific columns if provided
        if (!empty($columns) && $columns !== ['*']) :
            $query->select($columns);
        endif;

        $collection = $query->get();
        return [
            'count' => $collection->count(),
            'data' => $collection->toArray()
        ];
    }

    public function find($id, array $columns = ['*'])
    {
        $query = $this->model->query();

        // Determine the key to search by
        $key = (is_string($id) || !is_numeric($id)) ? 'public_id' : 'id';

        $query->where($key, $id);

        // Select specific columns if provided
        if (!empty($columns) && $columns !== ['*']) :
            $query->select($columns);
        endif;

        return $query->first();
    }

    public function findColumns($id, array $columns)
    {
        return $this->model->select($columns)->find($id);
    }

    public function store(array $data)
    {
        if (empty($data['public_id']) && in_array('public_id', $this->model->getFillable())) :
            $data['public_id'] = \Utils\StringUtil::uuid();
        endif;

        $result = $this->model->create($data);
        if (config('app.debug')) :
            $queryLog = Manager::connection()->getQueryLog();
            error_log(print_r($queryLog, true));
        endif;
        return $result;
    }

    public function update($id, array $data)
    {
        if (gettype($id) !== 'string' && is_numeric($id)) :
            return $this->model->where('id', $id)->update($data);
        endif;

        return $this->model->where('public_id', $id)->update($data);
    }

    public function delete($id)
    {
        if (gettype($id) !== 'string' && is_numeric($id)) :
            return $this->model->where('id', $id)->delete();
        endif;
        $result = $this->model->where('public_id', $id)->delete();

        if (config('app.debug')) :
            $queryLog = Manager::connection()->getQueryLog();
            error_log(print_r($queryLog, true));
        endif;
        return $result;
    }

    public function findWithRelations($id, array $relations = [])
    {
        $key = (is_string($id) || !is_numeric($id)) ? 'public_id' : 'id';
        $query = $this->model->query()->where($key, $id);

        if (!empty($relations)) :
            $query->with($relations);
        endif;

        return $query->first();
    }

    public function allWithRelations(array $relations = [], array $filters = [])
    {
        $query = $this->model->query();

        foreach ($filters as $field => $value) :
            if (!is_null($value)) :
                $query->where($field, $value);
            endif;
        endforeach;

        if (!empty($relations)) :
            $query->with($relations);
        endif;

        $collection = $query->get();
        return [
            'count' => $collection->count(),
            'data' => $collection->toArray()
        ];
    }

    public function first()
    {
        return $this->model->orderBy('id', 'asc')->first();
    }

    public function last()
    {
        return $this->model->orderByDesc('id')->first();
    }
}
