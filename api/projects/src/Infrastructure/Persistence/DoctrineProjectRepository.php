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
        $project = $this->entityManager->find(Project::class, $id);
        if ($project) {
            $project->checkAndUpdateStatus();
            $this->entityManager->flush();
        }
        return $project;
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

        // Always filter by active status (only show active projects)
        $qb->andWhere('p.isActive = :isActive')
           ->setParameter('isActive', true);

        $projects = $qb->getQuery()->getResult();

        // Update status for each project before returning
        foreach ($projects as $project) {
            $project->checkAndUpdateStatus();
        }
        $this->entityManager->flush();

        // Filter again to exclude projects that just became inactive
        return array_filter($projects, fn($p) => $p->isActive());
    }
}
