<?php

namespace Trekly\Participation\Application;

use Trekly\Participation\Domain\Participation\ParticipationRepository;
use Trekly\Participation\Domain\Participation\ParticipationStatus;

class UpdateParticipationStatusUseCase
{
    private ParticipationRepository $repository;

    public function __construct(ParticipationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $id, string $status): void
    {
        $participation = $this->repository->findById($id);
        if (!$participation) {
            throw new \Exception("Participation not found");
        }

        $statusEnum = ParticipationStatus::from($status);
        
        if ($statusEnum === ParticipationStatus::CONFIRMED) {
            $participation->confirm();
        } elseif ($statusEnum === ParticipationStatus::CANCELLED) {
            $participation->cancel();
        }

        $this->repository->save($participation);
    }
}
