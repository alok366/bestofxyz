<?php

namespace App\Contracts\Repositories;

interface ReadRepositoryInterface
{
    public function all(array $filters = [], array $columns = ['*']);
    public function find($id, array $columns = ['*']);
    public function findColumns($id, array $columns);
    public function findWithRelations($id, array $relations = []);
    public function allWithRelations(array $relations = [], array $filters = []);
    public function first();
    public function last();
}
