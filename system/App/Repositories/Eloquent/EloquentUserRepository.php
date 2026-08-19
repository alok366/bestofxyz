<?php

namespace App\Repositories\Eloquent;

use App\Models\User;

class EloquentUserRepository extends BaseEloquentRepository
{
    /**
     * @param User|null $model
     */
    public function __construct(?User $model = null)
    {
        $this->model = $model ?? new User();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', strtolower(trim($email)))->first();
    }

    public function findByUsername(string $username): ?User
    {
        return $this->model->where('username', trim($username))->first();
    }

    public function findByUsernameOrEmail(string $identifier): ?User
    {
        $identifier = trim($identifier);
        return $this->model->where('username', $identifier)
            ->orWhere('email', strtolower($identifier))
            ->first();
    }

    public function find($id, array $columns = ['*']): ?User
    {
        return $this->model->select($columns)->find($id);
    }
}
