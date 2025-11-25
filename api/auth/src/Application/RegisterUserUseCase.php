<?php

namespace Trekly\Auth\Application;

use Trekly\Auth\Domain\User\Role;
use Trekly\Auth\Domain\User\User;
use Trekly\Auth\Domain\User\UserRepository;

use Trekly\Auth\Infrastructure\Email\EmailService;

class RegisterUserUseCase
{
    private UserRepository $userRepository;
    private EmailService $emailService;

    public function __construct(UserRepository $userRepository, EmailService $emailService)
    {
        $this->userRepository = $userRepository;
        $this->emailService = $emailService;
    }

    public function execute(string $email, string $password, string $role): void
    {
        if ($this->userRepository->findByEmail($email)) {
            throw new \Exception("User already exists");
        }

        $roleEnum = Role::from($role);
        $user = User::create($email, $password, $roleEnum);

        $this->userRepository->save($user);

        // Send welcome email
        try {
            $this->emailService->sendWelcomeEmail($email, $password);
        } catch (\Exception $e) {
            // Log error but don't fail registration
            error_log("Failed to send welcome email: " . $e->getMessage());
        }
    }
}
