<?php

namespace Gotribe\Payments\Application;

use Gotribe\Payments\Domain\CuentaPorPagarRepository;

class CreateCuentaPorPagarUseCase
{
    public function __construct(private CuentaPorPagarRepository $repository) {}

    public function execute(string $creatorId, string $projectId, float $monto, string $fechaVencimiento): void
    {
        // Verificar si ya existe una cuenta por pagar para este creator y proyecto
        $cuenta = $this->repository->findByCreatorAndProject($creatorId, $projectId);
        if ($cuenta) {
            // Ya existe, no crear duplicado
            return;
        }

        // Crear nueva cuenta por pagar
        $this->repository->create([
            'id' => uniqid('', true),
            'creator_id' => $creatorId,
            'project_id' => $projectId,
            'monto' => $monto,
            'estado' => 'PENDIENTE',
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'fecha_vencimiento' => $fechaVencimiento,
            'fecha_pago' => null
        ]);
    }
}
