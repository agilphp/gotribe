<?php

namespace Gotribe\Payments\Interface\Http;

use Gotribe\Payments\Infrastructure\Persistence\PDOCuentaPorPagarRepository;

class PagoController
{
    public function __construct(private PDOCuentaPorPagarRepository $repository) {}

    public function registrarPago()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $cuentaId = $data['cuenta_por_pagar_id'] ?? null;
        $creatorId = $data['creator_id'] ?? null;
        $monto = $data['monto'] ?? null;
        $fechaPago = $data['fecha_pago'] ?? date('Y-m-d H:i:s');
        $metodoPago = $data['metodo_pago'] ?? null;
        $referenciaPago = $data['referencia_pago'] ?? null;

        if (!$cuentaId || !$creatorId || !$monto || !$metodoPago || !$referenciaPago) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            return;
        }

        $this->repository->registrarPago([
            'id' => uniqid('', true),
            'cuenta_por_pagar_id' => $cuentaId,
            'creator_id' => $creatorId,
            'monto' => $monto,
            'fecha_pago' => $fechaPago,
            'metodo_pago' => $metodoPago,
            'referencia_pago' => $referenciaPago
        ]);

        http_response_code(201);
        echo json_encode(['success' => true]);
    }

    // Lógica de vencimiento automática
    public function marcarCuentasVencidas()
    {
        $stmt = $this->repository->pdo->prepare('UPDATE cuentas_por_pagar SET estado = "VENCIDA" WHERE estado = "PENDIENTE" AND fecha_vencimiento < NOW()');
        $stmt->execute();
    }
}
