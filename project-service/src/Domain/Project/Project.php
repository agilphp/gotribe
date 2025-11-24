<?php

namespace Trekly\Project\Domain\Project;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'projects')]
class Project implements \JsonSerializable
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

    #[ORM\Column(name: 'max_guests', type: 'integer')]
    private int $maxGuests;

    #[ORM\Column(name: 'image_url', type: 'text', nullable: true)]
    private ?string $imageUrl;

    #[ORM\Column(name: 'is_published', type: 'boolean')]
    private bool $isPublished;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive;

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
        int $maxGuests = 10,
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
        $this->maxGuests = $maxGuests;
        $this->imageUrl = $imageUrl;
        $this->isPublished = false;
        $this->isActive = true;
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
        int $maxGuests = 10,
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
            $maxGuests,
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
    public function getMaxGuests(): int { return $this->maxGuests; }
    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function isPublished(): bool { return $this->isPublished; }
    public function isActive(): bool { return $this->isActive; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function checkAndUpdateStatus(): void
    {
        $now = new \DateTimeImmutable();
        if ($this->startDateTime < $now && $this->isActive) {
            $this->deactivate();
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'activityType' => $this->activityType->value,
            'creatorId' => $this->creatorId,
            'startDateTime' => $this->startDateTime->format(\DateTimeInterface::ATOM),
            'meetingPoint' => $this->meetingPoint,
            'price' => $this->price,
            'currency' => $this->currency,
            'maxGuests' => $this->maxGuests,
            'imageUrl' => $this->imageUrl,
            'isPublished' => $this->isPublished,
            'isActive' => $this->isActive,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM)
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
