<?php

namespace Trekly\Auth\Domain\Security;

use Trekly\Auth\Domain\User\User;

interface TokenProvider
{
    public function generateToken(User $user): string;
    public function validateToken(string $token): ?array;
}
