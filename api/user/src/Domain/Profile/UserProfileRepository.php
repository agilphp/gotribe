<?php

namespace Trekly\User\Domain\Profile;

interface UserProfileRepository
{
    public function save(UserProfile $profile): void;
    public function findByUserId(string $userId): ?UserProfile;
}
