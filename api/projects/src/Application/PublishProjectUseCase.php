<?php

namespace Trekly\Project\Application;

use Trekly\Project\Domain\Project\ProjectRepository;
use Trekly\Project\Infrastructure\Services\CreatorQRService;
use Trekly\Project\Infrastructure\Email\ProjectEmailService;
use Trekly\Project\Infrastructure\Http\AuthServiceClient;

class PublishProjectUseCase
{
    public function __construct(
        private ProjectRepository $repository,
        private CreatorQRService $qrService,
        private ProjectEmailService $emailService,
        private AuthServiceClient $authClient
    ) {}

    public function execute(string $id, string $creatorId): void
    {
        $project = $this->repository->findById($id);

        if (!$project) {
            throw new \Exception("Project not found");
        }

        if ($project->getCreatorId() !== $creatorId) {
            throw new \Exception("Unauthorized");
        }

        // Publish the project
        $project->publish();
        $this->repository->save($project);

        // Generate Creator QR Code (non-blocking)
        try {
            // Only generate QR if project has a price > 0
            if ($project->getPrice() > 0) {
                // Get creator details
                $creator = $this->authClient->getUser($creatorId);
                
                if ($creator && isset($creator['email'])) {
                    // Generate QR code
                    $qrDataUri = $this->qrService->generateCreatorQR(
                        $project->getId(),
                        $creatorId
                    );

                    // Generate PDF with QR
                    $pdfContent = $this->qrService->generateCreatorQRPdf(
                        [
                            'id' => $project->getId(),
                            'title' => $project->getTitle(),
                            'price' => $project->getPrice(),
                            'currency' => $project->getCurrency(),
                            'maxGuests' => $project->getMaxGuests()
                        ],
                        $qrDataUri
                    );

                    // Send email with QR PDF
                    $this->emailService->sendCreatorQR(
                        $creator['email'],
                        $creator['name'] ?? 'Creator',
                        [
                            'title' => $project->getTitle(),
                            'price' => $project->getPrice(),
                            'currency' => $project->getCurrency(),
                            'maxGuests' => $project->getMaxGuests()
                        ],
                        $pdfContent
                    );

                    error_log("Creator QR sent successfully for project: {$id}");
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the publication
            error_log("Failed to send creator QR: " . $e->getMessage());
        }
    }
}
