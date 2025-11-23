<?php

namespace Trekly\Auth\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Trekly\Auth\Domain\User\User;
use Trekly\Auth\Domain\User\UserRepository;

class DoctrineUserRepository implements UserRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);
    }

    public function findById(string $id): ?User
    {
        return $this->entityManager->find(User::class, $id);
    }
}
