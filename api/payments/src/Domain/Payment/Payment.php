<?php

namespace Trekly\Payments\Domain\Payment;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'payments')]
class Payment
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $projectId;

    #[ORM\Column(type: 'string', length: 36)]
    private string $userId;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $amount;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mpPreferenceId = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mpPaymentId = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $paymentMethod = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $payerEmail = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $errorMessage = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $paidAt = null;

    private function __construct(
        string $id,
        string $projectId,
        string $userId,
        float $amount,
        string $currency
    ) {
        $this->id = $id;
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->status = 'pending';
        $this->createdAt = new \DateTime();
    }

    public static function create(
        string $projectId,
        string $userId,
        float $amount,
        string $currency = 'COP'
    ): self {
        return new self(
            self::generateId(),
            $projectId,
            $userId,
            $amount,
            $currency
        );
    }

    private static function generateId(): string
    {
        return bin2hex(random_bytes(18));
    }

    // Getters
    public function getId(): string
    {
        return $this->id;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMercadoPagoPreferenceId(): ?string
    {
        return $this->mpPreferenceId;
    }

    public function getMercadoPagoPaymentId(): ?string
    {
        return $this->mpPaymentId;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function getPayerEmail(): ?string
    {
        return $this->payerEmail;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paidAt;
    }

    // Setters
    public function setStatus(string $status): void
    {
        $this->status = $status;
        
        if ($status === 'approved' && $this->paidAt === null) {
            $this->paidAt = new \DateTime();
        }
    }

    public function setMercadoPagoPreferenceId(string $preferenceId): void
    {
        $this->mpPreferenceId = $preferenceId;
    }

    public function setMercadoPagoPaymentId(string $paymentId): void
    {
        $this->mpPaymentId = $paymentId;
    }

    public function setPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;
    }

    public function setPayerEmail(string $email): void
    {
        $this->payerEmail = $email;
    }

    public function setErrorMessage(string $message): void
    {
        $this->errorMessage = $message;
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['rejected', 'cancelled', 'failed']);
    }
}
