<?php

namespace Trekly\Project\Domain\Project;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'projects')]
class Project
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private string $description;

    #[ORM\Column(name: 'activity_type', type: 'string', length: 50, enumType: ActivityType::class)]
    private ActivityType $activityType;

    #[ORM\Column(name: 'creator_id', type: 'string', length: 36)]
    private string $creatorId;

    #[ORM\Column(name: 'start_date_time', type: 'datetime_immutable')]
    private \DateTimeImmutable $startDateTime;

    #[ORM\Column(name: 'meeting_point', type: 'string', length: 255)]
    private string $meetingPoint;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(name: 'image_url', type: 'text', nullable: true)]
    private ?string $imageUrl;

    #[ORM\Column(name: 'is_published', type: 'boolean')]
    private bool $isPublished;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
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
