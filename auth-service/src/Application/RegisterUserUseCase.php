<?php

namespace Trekly\Auth\Application;

use Trekly\Auth\Domain\User\Role;
use Trekly\Auth\Domain\User\User;
use Trekly\Auth\Domain\User\UserRepository;

class RegisterUserUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(string $email, string $password, string $role): void
    {
        if ($this->userRepository->findByEmail($email)) {
            throw new \Exception("User already exists");
        }

        $roleEnum = Role::from($role);
        $user = User::create($email, $password, $roleEnum);

        $this->userRepository->save($user);
    }
}
