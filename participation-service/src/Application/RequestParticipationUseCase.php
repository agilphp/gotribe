<?php

namespace Trekly\Participation\Application;

use Trekly\Participation\Domain\Participation\Participation;
use Trekly\Participation\Domain\Participation\ParticipationRepository;

class RequestParticipationUseCase
{
    private ParticipationRepository $repository;

    public function __construct(ParticipationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $projectId, string $userId): Participation
    {
        $existing = $this->repository->findByProjectAndUser($projectId, $userId);
        if ($existing) {
            throw new \Exception("Already participating or requested");
        }

        $participation = Participation::request($projectId, $userId);
        $this->repository->save($participation);

        return $participation;
    }
}
