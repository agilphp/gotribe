<?php

namespace Trekly\Participation\Application;

use Trekly\Participation\Domain\Participation\Participation;
use Trekly\Participation\Domain\Participation\ParticipationRepository;
use Trekly\Participation\Infrastructure\Http\ProjectServiceClient;
use Trekly\Participation\Infrastructure\Http\AuthServiceClient;
use Trekly\Participation\Infrastructure\Email\EmailService;
use Trekly\Participation\Infrastructure\Services\TicketService;

class RequestParticipationUseCase
{
    public function __construct(
        private ParticipationRepository $repository,
        private ProjectServiceClient $projectClient,
        private AuthServiceClient $userClient,
        private EmailService $emailService,
        private TicketService $ticketService
    ) {}

    public function execute(string $projectId, string $userId): Participation
    {
        // Check if user already participated
        $existing = $this->repository->findByProjectAndUser($projectId, $userId);
        if ($existing) {
            throw new \Exception('You have already joined this project');
        }

        // Get project details to check max guests
        $project = $this->projectClient->getProject($projectId);
        if (!$project) {
            throw new \Exception('Project not found');
        }

        // Count current participants
        $currentParticipants = $this->repository->findByProjectId($projectId);
        $participantCount = count($currentParticipants);

        // Validate capacity
        $maxGuests = $project['maxGuests'] ?? 10;
        if ($participantCount >= $maxGuests) {
            throw new \Exception("Project is full. Maximum capacity: {$maxGuests} participants");
        }

        // Create participation
        $participation = Participation::request($projectId, $userId);
        $this->repository->save($participation);

        // Send email notifications (non-blocking - errors are logged but don't fail the request)
        try {
            // Get participant details from auth-service (has email)
            error_log("RequestParticipationUseCase - Fetching participant: {$userId}");
            $participant = $this->userClient->getUser($userId);
            error_log("RequestParticipationUseCase - Participant data: " . json_encode($participant));
            
            if ($participant && isset($participant['email'])) {
                // Generate PDF Ticket
                $pdfContent = $this->ticketService->generateTicketPdf(
                    [
                        'id' => $participation->getId(),
                        'userId' => $participation->getUserId(),
                        'projectId' => $participation->getProjectId()
                    ],
                    $project,
                    $participant
                );

                $this->emailService->sendParticipationConfirmation(
                    $participant['email'],
                    'Adventurer',
                    $project,
                    $pdfContent,
                    'Trekly_Ticket.pdf'
                );
            } else {
                error_log("RequestParticipationUseCase - Participant email not found or user null");
            }

            // Get creator details from auth-service
            $creator = $this->userClient->getUser($project['creatorId']);
            if ($creator && isset($creator['email'])) {
                $this->emailService->sendNewParticipantNotification(
                    $creator['email'],
                    'Creator',
                    'A new participant',
                    $project
                );
            }
        } catch (\Exception $e) {
            // Log email errors but don't fail the participation
            error_log("Email notification failed: " . $e->getMessage());
        }

        return $participation;
    }
}
