<?php

namespace Gotribe\Payments\Interface\Http;

use Gotribe\Payments\Infrastructure\Persistence\PDOCuentaPorPagarRepository;

class CuentaPorPagarConsultaController
{
    public function __construct(private PDOCuentaPorPagarRepository $repository) {}

    public function getCuentasPorCreator($creatorId)
    {
        $stmt = $this->repository->pdo->prepare('SELECT * FROM cuentas_por_pagar WHERE creator_id = :creator_id');
        $stmt->execute([':creator_id' => $creatorId]);
        $cuentas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        http_response_code(200);
        echo json_encode($cuentas);
    }
}
