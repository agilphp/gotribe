<?php

namespace Trekly\Auth\Application;

use Trekly\Auth\Domain\User\Email;
use Trekly\Auth\Domain\User\Password;
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
        $emailVo = new Email($email);
        
        if ($this->userRepository->findByEmail($emailVo)) {
            throw new \Exception("User already exists");
        }

        $passwordVo = Password::create($password);
        $roleEnum = Role::from($role);

        $user = User::create($emailVo, $passwordVo, $roleEnum);

        $this->userRepository->save($user);
    }
}
