<?php

namespace Trekly\Payments\Domain\Payment;

interface PaymentRepository
{
    public function save(Payment $payment): void;
    
    public function findById(string $id): ?Payment;
    
    public function findByProjectId(string $projectId): ?Payment;
    
    public function findByMercadoPagoPaymentId(string $mpPaymentId): ?Payment;
    
    public function findByUserId(string $userId): array;
}
