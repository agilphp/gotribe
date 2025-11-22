<?php

namespace Trekly\Project\Domain\Project;

class Project
{
    private string $id;
    private string $title;
    private string $description;
    private ActivityType $activityType;
    private string $creatorId;
    private \DateTimeImmutable $startDateTime;
    private string $meetingPoint;
    private float $price;
    private string $currency;
    private ?string $imageUrl;
    private bool $isPublished;
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $title,
        string $description,
        ActivityType $activityType,
        string $creatorId,
        \DateTimeImmutable $startDateTime,
        string $meetingPoint,
        float $price,
        string $currency,
        ?string $imageUrl = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->activityType = $activityType;
        $this->creatorId = $creatorId;
        $this->startDateTime = $startDateTime;
        $this->meetingPoint = $meetingPoint;
        $this->price = $price;
        $this->currency = $currency;
        $this->imageUrl = $imageUrl;
        $this->isPublished = false;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        string $title,
        string $description,
        ActivityType $activityType,
        string $creatorId,
        \DateTimeImmutable $startDateTime,
        string $meetingPoint,
        float $price,
        string $currency,
        ?string $imageUrl = null
    ): self {
        return new self(
            uniqid('', true),
            $title,
            $description,
            $activityType,
            $creatorId,
            $startDateTime,
            $meetingPoint,
            $price,
            $currency,
            $imageUrl
        );
    }

    public function publish(): void
    {
        $this->isPublished = true;
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getActivityType(): ActivityType { return $this->activityType; }
    public function getCreatorId(): string { return $this->creatorId; }
    public function getStartDateTime(): \DateTimeImmutable { return $this->startDateTime; }
    public function getMeetingPoint(): string { return $this->meetingPoint; }
    public function getPrice(): float { return $this->price; }
    public function getCurrency(): string { return $this->currency; }
    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function isPublished(): bool { return $this->isPublished; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
