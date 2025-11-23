<?php

namespace Trekly\Participation\Domain\Participation;

interface ParticipationRepository
{
    public function save(Participation $participation): void;
    public function findById(string $id): ?Participation;
    public function findByProjectAndUser(string $projectId, string $userId): ?Participation;
}
