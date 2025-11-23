<?php

namespace Trekly\User\Domain\Rating;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'creator_ratings')]
#[ORM\UniqueConstraint(name: 'unique_rating', columns: ['member_id', 'project_id'])]
class CreatorRating
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'creator_id', type: 'string', length: 36)]
    private string $creatorId;

    #[ORM\Column(name: 'member_id', type: 'string', length: 36)]
    private string $memberId;

    #[ORM\Column(name: 'project_id', type: 'string', length: 36)]
    private string $projectId;

    #[ORM\Column(type: 'integer')]
    private int $rating;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $creatorId,
        string $memberId,
        string $projectId,
        int $rating,
        ?string $comment = null
    ) {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }

        $this->id = $id;
        $this->creatorId = $creatorId;
        $this->memberId = $memberId;
        $this->projectId = $projectId;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): string { return $this->id; }
    public function getCreatorId(): string { return $this->creatorId; }
    public function getMemberId(): string { return $this->memberId; }
    public function getProjectId(): string { return $this->projectId; }
    public function getRating(): int { return $this->rating; }
    public function getComment(): ?string { return $this->comment; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
