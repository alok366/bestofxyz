<?php

namespace App\Services;

use App\Contracts\Repositories\WriteRepositoryInterface;
use App\Contracts\Repositories\ReadRepositoryInterface;

abstract class BaseService
{
    protected WriteRepositoryInterface $writeRepo;
    protected ReadRepositoryInterface $readRepo;

    public function __construct(
        WriteRepositoryInterface $writeRepo,
        ReadRepositoryInterface $readRepo
    ) {
        $this->writeRepo = $writeRepo;
        $this->readRepo = $readRepo;
    }

    public function all(array $filters = [], array $columns = ['*'])
    {
        return $this->readRepo->all($filters, $columns);
    }

    public function allWithRelations(array $relations = [], array $filters = [])
    {
        return $this->readRepo->allWithRelations($relations, $filters);
    }

    public function store(array $data)
    {
        return $this->writeRepo->store($data);
    }

    public function find($id, array $columns = ['*'])
    {
        return $this->readRepo->find($id, $columns);
    }

    public function findColumns($id, array $columns)
    {
        return $this->readRepo->findColumns($id, $columns);
    }

    public function findWithRelations($id, array $relations = [])
    {
        return $this->readRepo->findWithRelations($id, $relations);
    }

    public function update($id, array $data)
    {
        return $this->writeRepo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->writeRepo->delete($id);
    }

    public function first()
    {
        return $this->readRepo->first() ?: null;
    }

    public function last()
    {
        return $this->readRepo->last() ?: null;
    }
}
