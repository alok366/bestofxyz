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
        if (!$user) :
            return null;
        endif;

        if (!password_verify($password, $user->password_hash)) :
            return null;
        endif;

        return $user;
    }

    public function findById(int $id): ?User
    {
        return $this->userRepo->find($id);
    }
}
