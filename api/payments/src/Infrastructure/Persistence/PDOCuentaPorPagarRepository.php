<?php

namespace Gotribe\Payments\Infrastructure\Persistence;

use Gotribe\Payments\Domain\CuentaPorPagarRepository;

class PDOCuentaPorPagarRepository implements CuentaPorPagarRepository
{
    public function __construct(public \PDO $pdo) {}
    
    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO cuentas_por_pagar 
            (id, creator_id, project_id, monto, estado, fecha_creacion, fecha_vencimiento, fecha_pago)
            VALUES (:id, :creator_id, :project_id, :monto, :estado, :fecha_creacion, :fecha_vencimiento, :fecha_pago)
        ');
        
        $stmt->execute([
            ':id' => $data['id'],
            ':creator_id' => $data['creator_id'],
            ':project_id' => $data['project_id'],
            ':monto' => $data['monto'],
            ':estado' => $data['estado'],
            ':fecha_creacion' => $data['fecha_creacion'],
            ':fecha_vencimiento' => $data['fecha_vencimiento'],
            ':fecha_pago' => $data['fecha_pago']
        ]);
    }
    
    public function findByCreatorAndProject(string $creatorId, string $projectId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM cuentas_por_pagar 
            WHERE creator_id = :creator_id AND project_id = :project_id
            LIMIT 1
        ');
        
        $stmt->execute([
            ':creator_id' => $creatorId,
            ':project_id' => $projectId
        ]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function updateMonto(string $cuentaId, float $newMonto): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE cuentas_por_pagar 
            SET monto = :monto
            WHERE id = :id
        ');
        
        $stmt->execute([
            ':monto' => $newMonto,
            ':id' => $cuentaId
        ]);
    }
    
    public function registrarPago(array $data): void
    {
        // Insert payment record
        $stmt = $this->pdo->prepare('
            INSERT INTO pagos 
            (id, cuenta_por_pagar_id, creator_id, monto, fecha_pago, metodo_pago, referencia_pago)
            VALUES (:id, :cuenta_por_pagar_id, :creator_id, :monto, :fecha_pago, :metodo_pago, :referencia_pago)
        ');
        
        $stmt->execute([
            ':id' => $data['id'],
            ':cuenta_por_pagar_id' => $data['cuenta_por_pagar_id'],
            ':creator_id' => $data['creator_id'],
            ':monto' => $data['monto'],
            ':fecha_pago' => $data['fecha_pago'],
            ':metodo_pago' => $data['metodo_pago'],
            ':referencia_pago' => $data['referencia_pago']
        ]);
        
        // Update cuenta por pagar status
        $updateStmt = $this->pdo->prepare('
            UPDATE cuentas_por_pagar 
            SET estado = "PAGADA", fecha_pago = :fecha_pago
            WHERE id = :cuenta_id
        ');
        
        $updateStmt->execute([
            ':fecha_pago' => $data['fecha_pago'],
            ':cuenta_id' => $data['cuenta_por_pagar_id']
        ]);
    }
}
