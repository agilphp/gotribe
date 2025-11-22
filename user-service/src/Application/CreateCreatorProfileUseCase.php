<?php

namespace Trekly\User\Application;

use Trekly\User\Domain\Profile\CreatorProfile;
use Trekly\User\Domain\Profile\UserProfile;
use Trekly\User\Domain\Profile\UserProfileRepository;

class CreateCreatorProfileUseCase
{
    private UserProfileRepository $repository;

    public function __construct(UserProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $userId, string $type, string $documentInfo, ?string $companyName): void
    {
        $profile = $this->repository->findByUserId($userId);

        if (!$profile) {
            // If user profile doesn't exist, we might want to create a default one or throw error.
            // For now, let's assume we create a basic one or require it to exist.
            // Let's create a basic one.
            $profile = new UserProfile($userId, 'Unknown User'); 
        }

        $creatorProfile = new CreatorProfile($type, $documentInfo, $companyName, false);
        $profile->becomeCreator($creatorProfile);

        $this->repository->save($profile);
    }
}
