<?php

namespace Gotribe\Payments\Interface\Http;

use Gotribe\Payments\Application\CreateCuentaPorPagarUseCase;

class CuentaPorPagarController
{
    public function __construct(private CreateCuentaPorPagarUseCase $useCase) {}

    public function createCuentaPorPagar()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $creatorId = $data['creator_id'] ?? null;
        $projectId = $data['project_id'] ?? null;
        $monto = $data['monto'] ?? null;
        $fechaVencimiento = $data['fecha_vencimiento'] ?? null;

        if (!$creatorId || !$projectId || !$monto || !$fechaVencimiento) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            return;
        }

        $this->useCase->execute($creatorId, $projectId, $monto, $fechaVencimiento);

        http_response_code(201);
        echo json_encode(['success' => true]);
    }
}
