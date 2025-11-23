<?php

namespace Trekly\Auth\Application;

use Trekly\Auth\Domain\Security\TokenProvider;
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

    public function execute(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || !$user->verifyPassword($password)) {
            throw new \Exception("Invalid credentials");
        }

        $token = $this->tokenProvider->generateToken($user);

        return [
            'token' => $token,
            'role' => $user->getRole()->value,
            'userId' => $user->getId()
        ];
    }
}
