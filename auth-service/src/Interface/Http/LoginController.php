<?php

namespace Trekly\Auth\Interface\Http;

use Trekly\Auth\Application\LoginUserUseCase;

class LoginController
{
    private LoginUserUseCase $useCase;

    public function __construct(LoginUserUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        try {
            $token = $this->useCase->execute($data['email'], $data['password']);
            echo json_encode(['token' => $token]);
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
