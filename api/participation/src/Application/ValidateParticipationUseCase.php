<?php

namespace Trekly\Participation\Application;

use Trekly\Participation\Domain\Participation\ParticipationRepository;
use Trekly\Participation\Domain\Participation\ParticipationStatus;

class ValidateParticipationUseCase
{
    public function __construct(
        private ParticipationRepository $repository
    ) {}

    public function execute(string $participationId, string $creatorId): array
    {
        $participation = $this->repository->findById($participationId);

        if (!$participation) {
            throw new \Exception('Ticket not found');
        }

        // In a real app, we should verify that the creatorId owns the project
        // For now, we'll assume the gateway/controller checks if the user is a CREATOR
        
        if ($participation->getStatus() === ParticipationStatus::ATTENDED) {
            throw new \Exception('Ticket already used/validated');
        }

        if ($participation->getStatus() === ParticipationStatus::CANCELLED) {
            throw new \Exception('Ticket is cancelled');
        }

        // Mark as attended
        $participation->markAsAttended();
        $this->repository->save($participation);

        return [
            'valid' => true,
            'message' => 'Ticket validated successfully',
            'participantId' => $participation->getUserId(),
            'projectId' => $participation->getProjectId(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
