<?php

namespace Trekly\Auth\Infrastructure\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Trekly\Auth\Domain\Security\TokenProvider;
use Trekly\Auth\Domain\User\User;

class JwtTokenProvider implements TokenProvider
{
    private string $secretKey;
    private int $expirationTime;

    public function __construct(string $secretKey, int $expirationTime = 3600)
    {
        $this->secretKey = $secretKey;
        $this->expirationTime = $expirationTime;
    }

    public function generateToken(User $user): string
    {
        $payload = [
            'sub' => (string) $user->getId(),
            'email' => (string) $user->getEmail(),
            'role' => $user->getRole()->value,
            'iat' => time(),
            'exp' => time() + $this->expirationTime
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
