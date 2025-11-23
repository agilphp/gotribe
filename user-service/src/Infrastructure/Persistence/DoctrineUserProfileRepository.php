<?php

namespace Trekly\User\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Trekly\User\Domain\Profile\UserProfile;
use Trekly\User\Domain\Profile\UserProfileRepository;

class DoctrineUserProfileRepository implements UserProfileRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(UserProfile $profile): void
    {
        $this->entityManager->persist($profile);
        $this->entityManager->flush();
    }

    public function findByUserId(string $userId): ?UserProfile
    {
        return $this->entityManager->find(UserProfile::class, $userId);
    }
}
