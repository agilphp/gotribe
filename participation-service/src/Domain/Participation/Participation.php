<?php

namespace Trekly\Participation\Domain\Participation;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'participations')]
class Participation
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'project_id', type: 'string', length: 36)]
    private string $projectId;

    #[ORM\Column(name: 'user_id', type: 'string', length: 36)]
    private string $userId;

    #[ORM\Column(type: 'string', length: 50, enumType: ParticipationStatus::class)]
    private ParticipationStatus $status;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $projectId,
        string $userId,
        ParticipationStatus $status
    ) {
        $this->id = $id;
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->status = $status;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function request(string $projectId, string $userId): self
    {
        return new self(
            uniqid('', true),
            $projectId,
            $userId,
            ParticipationStatus::REQUESTED
        );
    }

    public function confirm(): void { $this->status = ParticipationStatus::CONFIRMED; }
    public function cancel(): void { $this->status = ParticipationStatus::CANCELLED; }

    public function getId(): string { return $this->id; }
    public function getProjectId(): string { return $this->projectId; }
    public function getUserId(): string { return $this->userId; }
    public function getStatus(): ParticipationStatus { return $this->status; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
