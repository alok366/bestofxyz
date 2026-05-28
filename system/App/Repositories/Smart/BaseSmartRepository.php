<?php

namespace App\Repositories\Smart;

use App\Contracts\Repositories\WriteRepositoryInterface;
use App\Contracts\Repositories\ReadRepositoryInterface;

abstract class BaseSmartRepository implements ReadRepositoryInterface, WriteRepositoryInterface
{
    protected $eloquent;

    public function all(array $filters = [], array $columns = ['*'])
    {
        return $this->eloquent->all($filters, $columns);
    }

    public function allWithRelations(array $relations = [], array $filters = [])
    {
        return $this->eloquent->allWithRelations($relations, $filters);
    }

    public function find($id, array $columns = ['*'])
    {
        return $this->eloquent->find($id, $columns);
    }

    public function findColumns($id, array $columns)
    {
        return $this->eloquent->findColumns($id, $columns);
    }

    public function findWithRelations($id, array $relations = [])
    {
        return $this->eloquent->findWithRelations($id, $relations);
    }

    public function store(array $data)
    {
        return $this->eloquent->store($data);
    }

    public function update($id, array $data)
    {
        return $this->eloquent->update($id, $data);
    }

    public function delete($id)
    {
        return $this->eloquent->delete($id);
    }

    public function first()
    {
        return $this->eloquent->first();
    }

    public function last()
    {
        return $this->eloquent->last();
    }
}
