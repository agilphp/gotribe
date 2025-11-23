<?php

namespace Trekly\Project\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Trekly\Project\Domain\Project\Project;
use Trekly\Project\Domain\Project\ProjectRepository;

class DoctrineProjectRepository implements ProjectRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Project $project): void
    {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Project
    {
        return $this->entityManager->find(Project::class, $id);
    }

    public function findAll(array $filters = []): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p')
           ->from(Project::class, 'p')
           ->where('1=1');

        if (isset($filters['activityType'])) {
            $qb->andWhere('p.activityType = :activityType')
               ->setParameter('activityType', $filters['activityType']);
        }

        if (isset($filters['isPublished'])) {
            $qb->andWhere('p.isPublished = :isPublished')
               ->setParameter('isPublished', (bool) $filters['isPublished']);
        }

        return $qb->getQuery()->getResult();
    }
}
