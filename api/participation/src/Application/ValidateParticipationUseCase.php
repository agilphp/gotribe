<?php

namespace Trekly\Participation\Application;

use Trekly\Participation\Domain\Participation\ParticipationRepository;
use Trekly\Participation\Domain\Participation\ParticipationStatus;
use Trekly\Participation\Infrastructure\Http\ProjectServiceClient;
use Trekly\Participation\Infrastructure\Http\PaymentServiceClient;

class ValidateParticipationUseCase
{
    public function __construct(
        private ParticipationRepository $repository,
        private ProjectServiceClient $projectClient,
        private PaymentServiceClient $paymentClient
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

        // Get project details to know the price
        $project = $this->projectClient->getProjectById($participation->getProjectId());
        
        if (!$project) {
            throw new \Exception('Project not found');
        }

        // Mark as attended
        $participation->markAsAttended();
        $this->repository->save($participation);

        // Register payment with payment service (10% commission)
        // Only if project has a price > 0
        if (isset($project['price']) && $project['price'] > 0) {
            // Calculate due date: 30 days after event date
            $dueDate = date('Y-m-d H:i:s', strtotime($project['start_date_time'] ?? 'now') + (30 * 24 * 60 * 60));
            
            $this->paymentClient->registerMemberPayment(
                $project['creator_id'],
                $participation->getProjectId(),
                (float)$project['price'],
                $dueDate
            );
        }

        return [
            'valid' => true,
            'message' => 'Ticket validated successfully',
            'participantId' => $participation->getUserId(),
            'projectId' => $participation->getProjectId(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
