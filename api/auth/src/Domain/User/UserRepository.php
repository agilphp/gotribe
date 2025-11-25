<?php

namespace Trekly\Auth\Domain\User;

interface UserRepository
{
    public function save(User $user): void;
    public function findByEmail(string $email): ?User;
}
