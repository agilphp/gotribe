<?php

namespace Trekly\Participation\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Trekly\Participation\Domain\Participation\Participation;
use Trekly\Participation\Domain\Participation\ParticipationRepository;

class DoctrineParticipationRepository implements ParticipationRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Participation $participation): void
    {
        $this->entityManager->persist($participation);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Participation
    {
        return $this->entityManager->find(Participation::class, $id);
    }

    public function findByProjectAndUser(string $projectId, string $userId): ?Participation
    {
        return $this->entityManager->getRepository(Participation::class)
            ->findOneBy([
                'projectId' => $projectId,
                'userId' => $userId
            ]);
    }

    public function findByProjectId(string $projectId): array
    {
        return $this->entityManager->getRepository(Participation::class)
            ->findBy(['projectId' => $projectId]);
    }

    public function findByUserId(string $userId): array
    {
        return $this->entityManager->getRepository(Participation::class)
            ->findBy(['userId' => $userId]);
    }
}
