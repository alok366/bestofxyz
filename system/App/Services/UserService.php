<?php

namespace App\Services;

use App\Repositories\Smart\SmartUserRepository;
use App\Models\User;

class UserService extends BaseService
{
    protected SmartUserRepository $userRepo;

    public function __construct(?SmartUserRepository $userRepo = null)
    {
        $this->userRepo = $userRepo ?? new SmartUserRepository();
        parent::__construct($this->userRepo, $this->userRepo);
    }

    public function register(string $username, string $email, string $password): User
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        return User::create([
            'username'      => trim($username),
            'email'         => strtolower(trim($email)),
            'password_hash' => $hash,
            'role'          => 'user',
        ]);
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userRepo->findByEmail($email);

        // Always run password_verify even when the user doesn't exist.
        // This prevents timing-based email enumeration attacks — bcrypt
        // takes constant time regardless of whether we have a real hash.
        $hash = $user->password_hash ?? '$2y$10$dummyhashtopreventtimingattackenumeration00000000000';

        if (!password_verify($password, $hash) || !$user) :
            return null;
        endif;

        return $user;
    }

    public function findById(int $id): ?User
    {
        return $this->userRepo->find($id);
    }
}
