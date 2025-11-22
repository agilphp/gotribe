<?php

namespace Trekly\User\Domain\Profile;

class UserProfile
{
    private string $userId;
    private string $fullName;
    private string $bio;
    private string $avatarUrl;
    private string $location;
    private ?CreatorProfile $creatorProfile;

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
        $this->creatorProfile = null;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getBio(): string
    {
        return $this->bio;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getCreatorProfile(): ?CreatorProfile
    {
        return $this->creatorProfile;
    }

    public function becomeCreator(CreatorProfile $creatorProfile): void
    {
        $this->creatorProfile = $creatorProfile;
    }

    public function updateProfile(string $fullName, string $bio, string $avatarUrl, string $location): void
    {
        $this->fullName = $fullName;
        $this->bio = $bio;
        $this->avatarUrl = $avatarUrl;
        $this->location = $location;
    }
}
