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
    }

    public function getUserId(): string { return $this->userId; }
    public function getFullName(): string { return $this->fullName; }
    public function getBio(): string { return $this->bio; }
    public function getAvatarUrl(): string { return $this->avatarUrl; }
    public function getLocation(): string { return $this->location; }
    public function isCreator(): bool { return $this->isCreator; }
    public function getSpecialty(): ?string { return $this->specialty; }
    public function getExperienceYears(): ?int { return $this->experienceYears; }

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
