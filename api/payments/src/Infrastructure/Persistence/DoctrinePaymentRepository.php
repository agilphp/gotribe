<?php

namespace Trekly\Payments\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Trekly\Payments\Domain\Payment\Payment;
use Trekly\Payments\Domain\Payment\PaymentRepository;

class DoctrinePaymentRepository implements PaymentRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Payment $payment): void
    {
        $this->entityManager->persist($payment);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Payment
    {
        return $this->entityManager->find(Payment::class, $id);
    }

    public function findByProjectId(string $projectId): ?Payment
    {
        return $this->entityManager->getRepository(Payment::class)
            ->findOneBy(['projectId' => $projectId]);
    }

    public function findByMercadoPagoPaymentId(string $mpPaymentId): ?Payment
    {
        return $this->entityManager->getRepository(Payment::class)
            ->findOneBy(['mpPaymentId' => $mpPaymentId]);
    }

    public function findByUserId(string $userId): array
    {
        return $this->entityManager->getRepository(Payment::class)
            ->findBy(['userId' => $userId], ['createdAt' => 'DESC']);
    }
}
