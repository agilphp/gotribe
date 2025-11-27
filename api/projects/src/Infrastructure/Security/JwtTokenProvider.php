<?php

namespace Trekly\Project\Infrastructure\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenProvider
{
    private string $secretKey;
    private int $expirationTime;

    public function __construct(string $secretKey, int $expirationTime = 3600)
    {
        $this->secretKey = $secretKey;
        $this->expirationTime = $expirationTime;
    }

    public function decodeToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
