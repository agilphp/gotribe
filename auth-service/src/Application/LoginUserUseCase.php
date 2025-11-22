<?php

namespace Trekly\Auth\Application;

use Trekly\Auth\Domain\Security\TokenProvider;
use Trekly\Auth\Domain\User\Email;
use Trekly\Auth\Domain\User\UserRepository;

class LoginUserUseCase
{
    private UserRepository $userRepository;
    private TokenProvider $tokenProvider;

    public function __construct(UserRepository $userRepository, TokenProvider $tokenProvider)
    {
        $this->userRepository = $userRepository;
        $this->tokenProvider = $tokenProvider;
    }

    public function execute(string $email, string $password): string
    {
        $emailVo = new Email($email);
        $user = $this->userRepository->findByEmail($emailVo);

        if (!$user || !$user->getPassword()->verify($password)) {
            throw new \Exception("Invalid credentials");
        }

        return $this->tokenProvider->generateToken($user);
    }
}
