<?php

namespace Trekly\User\Application;

use Trekly\User\Domain\Profile\UserProfile;
use Trekly\User\Domain\Profile\UserProfileRepository;

class GetUserProfileUseCase
{
    private UserProfileRepository $repository;

    public function __construct(UserProfileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $userId): ?UserProfile
    {
        return $this->repository->findByUserId($userId);
    }
}
