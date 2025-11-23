<?php

namespace Trekly\Project\Application;

use Trekly\Project\Domain\Project\ActivityType;
use Trekly\Project\Domain\Project\Project;
use Trekly\Project\Domain\Project\ProjectRepository;

class CreateProjectUseCase
{
    private ProjectRepository $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(
        string $title,
        string $description,
        string $activityType,
        string $creatorId,
        string $startDateTime,
        string $meetingPoint,
        float $price,
        string $currency,
        ?string $imageUrl = null
    ): Project {
        $project = Project::create(
            $title,
            $description,
            ActivityType::from($activityType),
            $creatorId,
            new \DateTimeImmutable($startDateTime),
            $meetingPoint,
            $price,
            $currency,
            10, // maxGuests - default value
            $imageUrl
        );
        
        // Auto-publish for MVP so it appears in the list immediately
        $project->publish();

        $this->repository->save($project);

        return $project;
    }
}
