<?php

namespace Trekly\User\Domain\Profile;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_profiles')]
class UserProfile
{
    #[ORM\Id]
    #[ORM\Column(name: 'user_id', type: 'string', length: 36)]
    private string $userId;

    #[ORM\Column(name: 'full_name', type: 'string', length: 255)]
    private string $fullName;

    #[ORM\Column(type: 'text')]
    private string $bio;

    #[ORM\Column(name: 'avatar_url', type: 'string', length: 255)]
    private string $avatarUrl;

    #[ORM\Column(type: 'string', length: 255)]
    private string $location;

    #[ORM\Column(name: 'is_creator', type: 'boolean')]
    private bool $isCreator;

    #[ORM\Column(name: 'specialty', type: 'string', length: 255, nullable: true)]
    private ?string $specialty;

    #[ORM\Column(name: 'experience_years', type: 'integer', nullable: true)]
    private ?int $experienceYears;

    #[ORM\Column(name: 'average_rating', type: 'decimal', precision: 3, scale: 2, options: ['default' => 0])]
    private float $averageRating;

    #[ORM\Column(name: 'total_ratings', type: 'integer', options: ['default' => 0])]
    private int $totalRatings;

    public function __construct(
        string $userId,
        string $fullName,
        string $bio = '',
        string $avatarUrl = '',
        string $location = ''
    ) {
        $this->userId = $userId;
        $this->fullName = $fullName;
        $this->bio = $bio;
        $this->avatarUrl = $avatarUrl;
        $this->location = $location;
        $this->isCreator = false;
        $this->specialty = null;
        $this->experienceYears = null;
        $this->averageRating = 0.0;
        $this->totalRatings = 0;
    }

    public function getUserId(): string { return $this->userId; }
    public function getFullName(): string { return $this->fullName; }
    public function getBio(): string { return $this->bio; }
    public function getAvatarUrl(): string { return $this->avatarUrl; }
    public function getLocation(): string { return $this->location; }
    public function isCreator(): bool { return $this->isCreator; }
    public function getSpecialty(): ?string { return $this->specialty; }
    public function getExperienceYears(): ?int { return $this->experienceYears; }
    public function getAverageRating(): float { return $this->averageRating; }
    public function getTotalRatings(): int { return $this->totalRatings; }

    public function updateRatingStats(float $newAverage, int $newTotal): void
    {
        $this->averageRating = $newAverage;
        $this->totalRatings = $newTotal;
    }

    public function becomeCreator(string $specialty, int $experienceYears): void
    {
        $this->isCreator = true;
        $this->specialty = $specialty;
        $this->experienceYears = $experienceYears;
    }

    public function updateProfile(string $fullName, string $bio, string $avatarUrl, string $location): void
    {
        $this->fullName = $fullName;
        $this->bio = $bio;
        $this->avatarUrl = $avatarUrl;
        $this->location = $location;
    }
}
