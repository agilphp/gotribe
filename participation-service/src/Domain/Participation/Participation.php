<?php

namespace Trekly\Participation\Domain\Participation;

class Participation
{
    private string $id;
    private string $projectId;
    private string $userId;
    private ParticipationStatus $status;
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

    public function confirm(): void
    {
        $this->status = ParticipationStatus::CONFIRMED;
    }

    public function cancel(): void
    {
        $this->status = ParticipationStatus::CANCELLED;
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getProjectId(): string { return $this->projectId; }
    public function getUserId(): string { return $this->userId; }
    public function getStatus(): ParticipationStatus { return $this->status; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
