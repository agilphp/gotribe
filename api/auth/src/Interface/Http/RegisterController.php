<?php

namespace Trekly\Auth\Interface\Http;

use Trekly\Auth\Application\RegisterUserUseCase;

class RegisterController
{
    private RegisterUserUseCase $useCase;

    public function __construct(RegisterUserUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email'], $data['password'], $data['role'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        try {
            $this->useCase->execute($data['email'], $data['password'], $data['role']);
            http_response_code(201);
            echo json_encode(['message' => 'User registered successfully']);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
