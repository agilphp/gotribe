<?php

namespace Trekly\Project\Domain\Project;

interface ProjectRepository
{
    public function save(Project $project): void;
    public function findById(string $id): ?Project;
    public function findAll(array $filters = []): array;
}
