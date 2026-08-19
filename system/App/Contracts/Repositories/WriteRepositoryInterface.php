<?php

namespace App\Contracts\Repositories;

interface WriteRepositoryInterface
{
    public function store(array $data);
    public function update($id, array $data);
    public function delete($id);
}
