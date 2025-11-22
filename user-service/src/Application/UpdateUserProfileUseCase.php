<?php

namespace Trekly\User\Application;

use Trekly\User\Domain\Profile\UserProfile;
use Trekly\User\Domain\Profile\UserProfileRepository;

class UpdateUserProfileUseCase
{
    private UserProfileRepository $repository;

    public function __construct(UserProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $userId, string $fullName, string $bio, string $avatarUrl, string $location): void
    {
        $profile = $this->repository->findByUserId($userId);

        if (!$profile) {
            $profile = new UserProfile($userId, $fullName, $bio, $avatarUrl, $location);
        } else {
            $profile->updateProfile($fullName, $bio, $avatarUrl, $location);
        }

        $this->repository->save($profile);
    }
}
