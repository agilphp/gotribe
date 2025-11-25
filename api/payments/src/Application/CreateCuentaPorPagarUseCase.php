<?php

namespace Gotribe\Payments\Application;

use Gotribe\Payments\Domain\CuentaPorPagarRepository;

class CreateCuentaPorPagarUseCase
{
    public function __construct(private CuentaPorPagarRepository $repository) {}

    /**
     * Creates or updates a cuenta por pagar for a creator
     * This represents the 10% commission the creator owes to GoTribe
     * 
     * @param string $creatorId Creator who owes the commission
     * @param string $projectId Project related to the payment
     * @param float $memberPaymentAmount Amount paid by the member to the creator
     * @param string $fechaVencimiento Due date for the commission payment
     */
    public function execute(string $creatorId, string $projectId, float $memberPaymentAmount, string $fechaVencimiento): void
    {
        // Calculate 10% commission that creator owes to GoTribe
        $commission = $memberPaymentAmount * 0.10;
        
        // Check if cuenta already exists for this creator and project
        $cuenta = $this->repository->findByCreatorAndProject($creatorId, $projectId);
        
        if ($cuenta) {
            // Update existing cuenta: add the new commission to the total
            $newTotal = $cuenta['monto'] + $commission;
            $this->repository->updateMonto($cuenta['id'], $newTotal);
        } else {
            // Create new cuenta por pagar
            $this->repository->create([
                'id' => uniqid('cta_', true),
                'creator_id' => $creatorId,
                'project_id' => $projectId,
                'monto' => $commission,
                'estado' => 'PENDIENTE',
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'fecha_vencimiento' => $fechaVencimiento,
                'fecha_pago' => null
            ]);
        }
    }
}
