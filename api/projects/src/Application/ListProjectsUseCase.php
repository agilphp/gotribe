<?php

namespace Trekly\Project\Application;

use Trekly\Project\Domain\Project\ProjectRepository;

class ListProjectsUseCase
{
    private ProjectRepository $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $filters): array
    {
        // By default only show published projects unless specified otherwise (e.g. for admin)
        // For MVP public list:
        $filters['isPublished'] = true;
        return $this->repository->findAll($filters);
    }
}
