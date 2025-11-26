<?php

namespace Trekly\Payments\Infrastructure\Persistence;

use Trekly\Payments\Domain\Payment\Payment;
use Trekly\Payments\Domain\Payment\PaymentRepository;
use PDO;

class PDOPaymentRepository implements PaymentRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Payment $payment): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO payments (
                id, project_id, user_id, amount, currency, status,
                mp_preference_id, mp_payment_id, payment_method, payer_email,
                error_message, created_at, paid_at
            ) VALUES (
                :id, :project_id, :user_id, :amount, :currency, :status,
                :mp_preference_id, :mp_payment_id, :payment_method, :payer_email,
                :error_message, :created_at, :paid_at
            ) ON DUPLICATE KEY UPDATE
                status = :status,
                mp_preference_id = :mp_preference_id,
                mp_payment_id = :mp_payment_id,
                payment_method = :payment_method,
                payer_email = :payer_email,
                error_message = :error_message,
                paid_at = :paid_at
        ");

        $stmt->execute([
            'id' => $payment->getId(),
            'project_id' => $payment->getProjectId(),
            'user_id' => $payment->getUserId(),
            'amount' => $payment->getAmount(),
            'currency' => $payment->getCurrency(),
            'status' => $payment->getStatus(),
            'mp_preference_id' => $payment->getMercadoPagoPreferenceId(),
            'mp_payment_id' => $payment->getMercadoPagoPaymentId(),
            'payment_method' => $payment->getPaymentMethod(),
            'payer_email' => $payment->getPayerEmail(),
            'error_message' => null,
            'created_at' => $payment->getCreatedAt()->format('Y-m-d H:i:s'),
            'paid_at' => $payment->getPaidAt()?->format('Y-m-d H:i:s')
        ]);
    }

    public function findById(string $id): ?Payment
    {
        $stmt = $this->pdo->prepare("SELECT * FROM payments WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    public function findByProjectId(string $projectId): ?Payment
    {
        $stmt = $this->pdo->prepare("SELECT * FROM payments WHERE project_id = :project_id");
        $stmt->execute(['project_id' => $projectId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    public function findByMercadoPagoPaymentId(string $mpPaymentId): ?Payment
    {
        $stmt = $this->pdo->prepare("SELECT * FROM payments WHERE mp_payment_id = :mp_payment_id");
        $stmt->execute(['mp_payment_id' => $mpPaymentId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->hydrate($row) : null;
    }

    public function findByUserId(string $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM payments WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    private function hydrate(array $row): Payment
    {
        // Usar reflexión para crear el objeto Payment sin constructor público
        $reflection = new \ReflectionClass(Payment::class);
        $payment = $reflection->newInstanceWithoutConstructor();

        // Setear propiedades privadas
        $this->setProperty($payment, 'id', $row['id']);
        $this->setProperty($payment, 'projectId', $row['project_id']);
        $this->setProperty($payment, 'userId', $row['user_id']);
        $this->setProperty($payment, 'amount', (float)$row['amount']);
        $this->setProperty($payment, 'currency', $row['currency']);
        $this->setProperty($payment, 'status', $row['status']);
        $this->setProperty($payment, 'mpPreferenceId', $row['mp_preference_id']);
        $this->setProperty($payment, 'mpPaymentId', $row['mp_payment_id']);
        $this->setProperty($payment, 'paymentMethod', $row['payment_method']);
        $this->setProperty($payment, 'payerEmail', $row['payer_email']);
        $this->setProperty($payment, 'createdAt', new \DateTime($row['created_at']));
        $this->setProperty($payment, 'paidAt', $row['paid_at'] ? new \DateTime($row['paid_at']) : null);

        return $payment;
    }

    private function setProperty(object $object, string $property, $value): void
    {
        $reflection = new \ReflectionProperty(get_class($object), $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }
}
