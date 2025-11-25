<?php

namespace Trekly\Project\Application;

use Trekly\Project\Domain\Project\ProjectRepository;

class PublishProjectUseCase
{
    private ProjectRepository $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $id, string $creatorId): void
    {
        $project = $this->repository->findById($id);

        if (!$project) {
            throw new \Exception("Project not found");
        }

        if ($project->getCreatorId() !== $creatorId) {
            throw new \Exception("Unauthorized");
        }

        $project->publish();
        $this->repository->save($project);
    }
}
