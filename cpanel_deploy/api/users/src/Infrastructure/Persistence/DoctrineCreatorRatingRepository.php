<?php

namespace Trekly\User\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Trekly\User\Domain\Rating\CreatorRating;
use Trekly\User\Domain\Rating\CreatorRatingRepository;

class DoctrineCreatorRatingRepository implements CreatorRatingRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(CreatorRating $rating): void
    {
        $this->entityManager->persist($rating);
        $this->entityManager->flush();
    }

    public function findByCreatorId(string $creatorId): array
    {
        return $this->entityManager->getRepository(CreatorRating::class)
            ->findBy(['creatorId' => $creatorId], ['createdAt' => 'DESC']);
    }

    public function findByMemberId(string $memberId): array
    {
        return $this->entityManager->getRepository(CreatorRating::class)
            ->findBy(['memberId' => $memberId], ['createdAt' => 'DESC']);
    }

    public function findByMemberAndProject(string $memberId, string $projectId): ?CreatorRating
    {
        return $this->entityManager->getRepository(CreatorRating::class)
            ->findOneBy(['memberId' => $memberId, 'projectId' => $projectId]);
    }

    public function getAverageRating(string $creatorId): float
    {
        $qb = $this->entityManager->createQueryBuilder();
        $result = $qb->select('avg(r.rating)')
            ->from(CreatorRating::class, 'r')
            ->where('r.creatorId = :creatorId')
            ->setParameter('creatorId', $creatorId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    public function countRatings(string $creatorId): int
    {
        return $this->entityManager->getRepository(CreatorRating::class)
            ->count(['creatorId' => $creatorId]);
    }
}
