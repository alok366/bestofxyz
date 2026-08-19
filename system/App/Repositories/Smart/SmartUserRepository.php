<?php

namespace App\Repositories\Smart;

use App\Repositories\Eloquent\EloquentUserRepository;
use App\Models\User;

class SmartUserRepository extends BaseSmartRepository
{
    protected $eloquent;

    public function __construct(?EloquentUserRepository $eloquent = null)
    {
        $this->eloquent = $eloquent ?? new EloquentUserRepository();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->eloquent->findByEmail($email);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->eloquent->findByUsername($username);
    }

    public function findByUsernameOrEmail(string $identifier): ?User
    {
        return $this->eloquent->findByUsernameOrEmail($identifier);
    }

    public function findByUsernameOrId($identifier): ?User
    {
        if (is_numeric($identifier)) :
            return $this->eloquent->find((int) $identifier);
        endif;
        return $this->eloquent->findByUsername((string) $identifier);
    }
}
